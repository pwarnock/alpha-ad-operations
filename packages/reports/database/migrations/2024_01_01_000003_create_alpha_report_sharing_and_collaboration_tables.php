<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Alpha\Reports\Services\EnvironmentDetectionService;

return new class extends Migration
{
    protected EnvironmentDetectionService $envDetector;
    protected bool $isMultiTenant;
    protected bool $hasSaaSykit;
    protected bool $hasLaravelTenancy;

    public function __construct()
    {
        $this->envDetector = app(EnvironmentDetectionService::class);
        $this->envDetector->detect();
        
        $this->isMultiTenant = $this->envDetector->hasFeature('tenant_isolation');
        $this->hasSaaSykit = $this->envDetector->isSaaSykitAvailable();
        $this->hasLaravelTenancy = $this->envDetector->isLaravelTenancyAvailable();
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alpha_report_shares', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Relationships
            $table->foreignId('report_id')->constrained('alpha_reports')->onDelete('cascade');
            
            // Share configuration
            $table->string('share_token')->unique();
            $table->enum('access_level', ['view', 'comment', 'edit'])->default('view');
            $table->json('permissions')->nullable(); // Granular permissions
            
            // Access control
            $table->string('share_type'); // public, private, restricted, organization
            $table->json('allowed_emails')->nullable(); // Email-based access
            $table->json('allowed_domains')->nullable(); // Domain-based access
            $table->json('allowed_roles')->nullable(); // Role-based access
            
            // Security settings
            $table->boolean('require_authentication')->default(true);
            $table->string('password')->nullable(); // Password-protected shares
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_access_count')->nullable();
            $table->integer('current_access_count')->default(0);
            
            // User ownership
            $this->addUserColumns($table);
            
            // Usage tracking (conditional)
            $this->addUsageColumns($table);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $this->addIndexes($table);
        });

        Schema::create('alpha_report_access_logs', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Relationships
            $table->foreignId('report_id')->constrained('alpha_reports')->onDelete('cascade');
            $table->foreignId('share_id')->nullable()->constrained('alpha_report_shares')->onDelete('set null');
            
            // Access information
            $table->string('access_id')->unique(); // UUID for tracking
            $table->string('access_type'); // view, export, share, comment
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            
            // User information (if authenticated)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_email')->nullable();
            $table->string('user_name')->nullable();
            
            // Access details
            $table->json('access_details')->nullable(); // Export format, filters used, etc.
            $table->integer('duration_seconds')->nullable(); // Time spent viewing
            $table->string('referrer')->nullable();
            
            // Timestamps
            $table->timestamp('accessed_at');
            $table->timestamps();
            
            // Indexes
            $this->addIndexes($table);
        });

        Schema::create('alpha_report_comments', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Relationships
            $table->foreignId('report_id')->constrained('alpha_reports')->onDelete('cascade');
            $table->foreignId('share_id')->nullable()->constrained('alpha_report_shares')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('alpha_report_comments')->onDelete('cascade');
            
            // Comment content
            $table->text('content');
            $table->enum('comment_type', ['comment', 'annotation', 'suggestion'])->default('comment');
            $table->json('context')->nullable(); // Page number, chart reference, etc.
            
            // User information
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            
            // Status and moderation
            $table->enum('status', ['published', 'pending', 'hidden', 'deleted'])->default('published');
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $this->addIndexes($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alpha_report_comments');
        Schema::dropIfExists('alpha_report_access_logs');
        Schema::dropIfExists('alpha_report_shares');
    }

    /**
     * Add tenant-specific columns based on environment.
     */
    protected function addTenantColumns(Blueprint $table): void
    {
        if (!$this->isMultiTenant) {
            return;
        }

        if ($this->hasSaaSykit) {
            // SaaSykit tenant structure
            $tenantModel = config('reports.saasykit.tenant.model', 'App\\Models\\Tenant');
            $tenantTable = (new $tenantModel)->getTable();
            
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained($tenantTable)
                ->onDelete('cascade');

            // SaaSykit organization structure
            if (class_exists('App\\Models\\Organization')) {
                $table->foreignId('organization_id')
                    ->nullable()
                    ->after('tenant_id')
                    ->constrained()
                    ->onDelete('cascade');

                $table->foreignId('organizational_unit_id')
                    ->nullable()
                    ->after('organization_id')
                    ->constrained()
                    ->onDelete('cascade');
            }
        } elseif ($this->hasLaravelTenancy) {
            // Generic Laravel Tenancy
            $table->string('tenant_id')->nullable()->after('id');
            $table->index('tenant_id');
        }
    }

    /**
     * Add user ownership columns.
     */
    protected function addUserColumns(Blueprint $table): void
    {
        $userModel = config('reports.saasykit.user.model', 'App\\Models\\User');
        $userTable = (new $userModel)->getTable();
        
        $table->foreignId('created_by')
            ->nullable()
            ->constrained($userTable)
            ->onDelete('set null');

        $table->foreignId('updated_by')
            ->nullable()
            ->after('created_by')
            ->constrained($userTable)
            ->onDelete('set null');
    }

    /**
     * Add usage tracking columns (SaaSykit only).
     */
    protected function addUsageColumns(Blueprint $table): void
    {
        if (!$this->hasSaaSykit) {
            return;
        }

        // Usage tracking
        $table->string('subscription_feature')->nullable();
        $table->json('subscription_limits')->nullable();
        $table->timestamp('last_usage_tracked_at')->nullable();
    }

    /**
     * Add appropriate indexes based on environment.
     */
    protected function addIndexes(Blueprint $table): void
    {
        // Basic indexes
        $table->index(['created_at']);
        
        // Table-specific indexes
        if ($table->getTable() === 'alpha_report_shares') {
            $table->index(['report_id', 'share_type']);
            $table->index(['share_token']);
            $table->index(['expires_at', 'status']);
            $table->index(['access_level', 'share_type']);
        } elseif ($table->getTable() === 'alpha_report_access_logs') {
            $table->index(['report_id', 'accessed_at']);
            $table->index(['share_id', 'accessed_at']);
            $table->index(['access_id']);
            $table->index(['access_type', 'accessed_at']);
            $table->index(['user_id', 'accessed_at']);
            $table->index(['ip_address']);
            $table->index(['accessed_at']);
        } elseif ($table->getTable() === 'alpha_report_comments') {
            $table->index(['report_id', 'status']);
            $table->index(['parent_id']);
            $table->index(['user_id', 'status']);
            $table->index(['comment_type', 'status']);
        }
        
        // Tenant-specific indexes
        if ($this->isMultiTenant) {
            if ($this->hasSaaSykit) {
                $table->index(['tenant_id']);
                $table->index(['organization_id']);
                
                if (class_exists('App\\Models\\Organization')) {
                    $table->index(['organization_id', 'organizational_unit_id']);
                }
            } elseif ($this->hasLaravelTenancy) {
                $table->index(['tenant_id']);
            }
        }

        // Usage tracking indexes (SaaSykit only)
        if ($this->hasSaaSykit) {
            $table->index(['subscription_feature']);
            $table->index(['last_usage_tracked_at']);
        }
    }
};