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
        Schema::create('alpha_reports', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Core report information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('report_type');
            $table->string('category')->default('general');
            
            // Report configuration and data
            $table->json('configuration');
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->json('chart_config')->nullable();
            
            // Status and scheduling
            $table->enum('status', ['draft', 'active', 'archived', 'scheduled'])->default('draft');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->json('schedule_config')->nullable();
            
            // User ownership and permissions
            $this->addUserColumns($table);
            
            // Performance and caching
            $table->json('cache_config')->nullable();
            $table->integer('cache_ttl')->default(3600);
            $table->timestamp('last_cached_at')->nullable();
            $table->string('cache_key')->nullable();
            
            // Usage tracking (conditional)
            $this->addUsageColumns($table);
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->string('version')->default('1.0');
            
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
        Schema::dropIfExists('alpha_reports');
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

        // Owner for permission checks
        $table->foreignId('owner_id')
            ->nullable()
            ->after('updated_by')
            ->constrained($userTable)
            ->onDelete('cascade');
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
        $table->integer('view_count')->default(0);
        $table->integer('export_count')->default(0);
        $table->integer('share_count')->default(0);
        $table->json('usage_stats')->nullable();
        
        // Subscription limits tracking
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
        $table->index(['status', 'report_type']);
        $table->index(['is_public', 'status']);
        $table->index(['owner_id', 'status']);
        $table->index(['slug']);
        $table->index(['category']);
        
        // Tenant-specific indexes
        if ($this->isMultiTenant) {
            if ($this->hasSaaSykit) {
                $table->index(['tenant_id', 'status']);
                $table->index(['organization_id', 'report_type']);
                $table->index(['tenant_id', 'owner_id']);
                
                if (class_exists('App\\Models\\Organization')) {
                    $table->index(['organization_id', 'organizational_unit_id', 'status']);
                }
            } elseif ($this->hasLaravelTenancy) {
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'owner_id']);
            }
        }

        // Performance indexes
        $table->index(['is_scheduled', 'status']);
        $table->index(['last_cached_at']);
        $table->index(['created_at']);
        
        // Usage tracking indexes (SaaSykit only)
        if ($this->hasSaaSykit) {
            $table->index(['subscription_feature']);
            $table->index(['last_usage_tracked_at']);
        }

        // Full-text search indexes (if supported)
        $this->addFullTextIndexes($table);
    }

    /**
     * Add full-text search indexes for supported databases.
     */
    protected function addFullTextIndexes(Blueprint $table): void
    {
        $databaseDriver = config('database.default');
        $driver = config("database.connections.{$databaseDriver}.driver");

        if (!in_array($driver, ['mysql', 'pgsql'])) {
            return; // Only MySQL and PostgreSQL support full-text indexes
        }

        try {
            if ($driver === 'mysql') {
                // MySQL full-text indexes
                $table->rawIndex('FULLTEXT(name, description)', 'alpha_reports_name_description_fulltext');
            } elseif ($driver === 'pgsql') {
                // PostgreSQL full-text search indexes
                $table->rawIndex(
                    "to_tsvector('english', name || ' ' || COALESCE(description, ''))",
                    'alpha_reports_search_vector'
                );
            }
        } catch (\Exception $e) {
            // Log error but don't fail migration
            \Log::warning('Could not create full-text index for alpha_reports: ' . $e->getMessage());
        }
    }
};