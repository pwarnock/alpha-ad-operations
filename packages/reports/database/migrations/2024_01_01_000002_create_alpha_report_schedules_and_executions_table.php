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
        Schema::create('alpha_report_schedules', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Relationship to the report
            $table->foreignId('report_id')->constrained('alpha_reports')->onDelete('cascade');
            
            // Schedule configuration
            $table->string('name');
            $table->string('frequency'); // hourly, daily, weekly, monthly, yearly, custom
            $table->json('schedule_config'); // Cron expression, specific times, etc.
            $table->string('timezone')->default('UTC');
            
            // Delivery configuration
            $table->json('delivery_config'); // Email, webhook, etc.
            $table->json('export_config'); // Format, filters, etc.
            
            // Status and timing
            $table->enum('status', ['active', 'paused', 'completed', 'failed'])->default('active');
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('ends_at')->nullable(); // For schedules with end dates
            
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

        Schema::create('alpha_report_executions', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Multi-tenancy columns (conditional)
            $this->addTenantColumns($table);
            
            // Relationships
            $table->foreignId('report_id')->constrained('alpha_reports')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('alpha_report_schedules')->onDelete('set null');
            
            // Execution details
            $table->string('execution_id')->unique(); // UUID for tracking
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('trigger_type'); // manual, scheduled, api, webhook
            
            // Configuration snapshot
            $table->json('configuration_snapshot'); // Report config at time of execution
            $table->json('filters_snapshot')->nullable();
            $table->json('export_config')->nullable();
            
            // Timing information
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            
            // Results and output
            $table->json('result_summary')->nullable(); // Row counts, metrics, etc.
            $table->string('output_path')->nullable(); // Path to generated file
            $table->string('output_format')->nullable(); // csv, excel, pdf, etc.
            $table->integer('file_size_bytes')->nullable();
            
            // Error handling
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            $table->integer('retry_count')->default(0);
            
            // User ownership
            $this->addUserColumns($table);
            
            // Usage tracking (conditional)
            $this->addUsageColumns($table);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $this->addIndexes($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alpha_report_executions');
        Schema::dropIfExists('alpha_report_schedules');
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
        $table->index(['status']);
        $table->index(['created_at']);
        
        // Table-specific indexes
        if ($table->getTable() === 'alpha_report_schedules') {
            $table->index(['report_id', 'status']);
            $table->index(['frequency', 'status']);
            $table->index(['next_run_at', 'status']);
            $table->index(['status', 'next_run_at']);
        } elseif ($table->getTable() === 'alpha_report_executions') {
            $table->index(['report_id', 'status']);
            $table->index(['schedule_id', 'status']);
            $table->index(['execution_id']);
            $table->index(['status', 'started_at']);
            $table->index(['trigger_type', 'status']);
            $table->index(['started_at', 'status']);
        }
        
        // Tenant-specific indexes
        if ($this->isMultiTenant) {
            if ($this->hasSaaSykit) {
                $table->index(['tenant_id', 'status']);
                $table->index(['organization_id', 'status']);
                
                if (class_exists('App\\Models\\Organization')) {
                    $table->index(['organization_id', 'organizational_unit_id', 'status']);
                }
            } elseif ($this->hasLaravelTenancy) {
                $table->index(['tenant_id', 'status']);
            }
        }

        // Usage tracking indexes (SaaSykit only)
        if ($this->hasSaaSykit) {
            $table->index(['subscription_feature']);
            $table->index(['last_usage_tracked_at']);
        }
    }
};