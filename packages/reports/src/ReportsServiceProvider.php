<?php

namespace Alpha\Reports;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Alpha\Reports\Services\ReportBuilderService;
use Alpha\Reports\Services\ChartDataService;
use Alpha\Reports\Services\ReportFilterService;
use Alpha\Reports\Services\SaaSykitReportsService;
use Alpha\Reports\Services\EnvironmentDetectionService;
use Alpha\Reports\Services\ConfigurationManager;
use Alpha\Reports\Services\MigrationManager;
use Alpha\Reports\Commands\ValidateConfigurationCommand;
use Alpha\Reports\Commands\ManageMigrationsCommand;
use Alpha\Reports\Contracts\ReportBuilderServiceInterface;

class ReportsServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        ReportFilterService::class => ReportFilterService::class,
        SaaSykitReportsService::class => SaaSykitReportsService::class,
        EnvironmentDetectionService::class => EnvironmentDetectionService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/reports.php',
            'reports'
        );

        // Register environment detection service first
        $this->app->singleton(EnvironmentDetectionService::class, function ($app) {
            return new EnvironmentDetectionService();
        });

        // Register configuration manager with environment detection
        $this->app->singleton(ConfigurationManager::class, function ($app) {
            return new ConfigurationManager($app->make(EnvironmentDetectionService::class));
        });

        // Register migration manager
        $this->app->singleton(MigrationManager::class, function ($app) {
            return new MigrationManager(
                $app->make(EnvironmentDetectionService::class),
                $app->make('migrator')
            );
        });

        // Register SaaSykit compatibility services
        $this->registerSaaSykitServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Initialize environment detection and configuration
        $this->initializePackageConfiguration();

        $this->loadViewsFrom(__DIR__.'/Views', 'reports');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // Register routes if they exist
        if (file_exists(__DIR__.'/routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }

        $this->publishes([
            __DIR__.'/Config/reports.php' => config_path('reports.php'),
        ], 'reports-config');

        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/reports'),
        ], 'reports-views');

        $this->publishes([
            __DIR__.'/Database/Migrations' => database_path('migrations'),
        ], 'reports-migrations');

        $this->publishes([
            __DIR__.'/../resources' => public_path('vendor/reports'),
        ], 'reports-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ValidateConfigurationCommand::class,
                ManageMigrationsCommand::class,
            ]);
        }

        // Register SaaSykit integration
        $this->registerSaaSykitIntegration();
        
        // Register event listeners
        $this->registerEventListeners();
    }

/**
     * Initialize package configuration.
     */
    protected function initializePackageConfiguration(): void
    {
        // Initialize environment detection
        $envDetection = $this->app->make(EnvironmentDetectionService::class);
        
        // Initialize configuration manager
        $configManager = $this->app->make(ConfigurationManager::class);
        
        // Log configuration summary
        if ($this->app->bound('log')) {
            $this->app['log']->info('Alpha Reports package initialized', [
                'environment_summary' => $envDetection->getSummary(),
                'recommendations' => $configManager->getRecommendations(),
            ]);
        }
    }

    /**
     * Register SaaSykit-specific services.
     */
    protected function registerSaaSykitServices(): void
    {
        // SaaSykit services will be registered when available
    }

    /**
    * Register SaaSykit integration hooks.
    */
    protected function registerSaaSykitIntegration(): void
    {
    // SaaSykit integration will be added when available
    }

    /**
     * Register package event listeners.
     */
    protected function registerEventListeners(): void
    {
        // Register package-specific event listeners
        $this->app['events']->listen('reports.generated', function ($event) {
            // Handle report generation completion
            if ($this->app->bound('log')) {
                $this->app['log']->info('Report generated', [
                    'report_id' => $event->report->id,
                    'tenant_id' => $event->report->tenant_id,
                    'user_id' => $event->report->user_id,
                ]);
            }
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            ReportFilterService::class,
            SaaSykitReportsService::class,
            EnvironmentDetectionService::class,
            ConfigurationManager::class,
            MigrationManager::class,
            'alpha.reports.tenant',
            'alpha.reports.subscription',
            'alpha.reports.config',
            'alpha.reports.environment',
            'alpha.reports.migration',
        ];
    }
}