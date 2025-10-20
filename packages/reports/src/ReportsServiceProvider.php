<?php

namespace Alpha\Reports;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Alpha\Reports\Services\ReportBuilderService;
use Alpha\Reports\Services\ChartDataService;
use Alpha\Reports\Services\ReportFilterService;
use Alpha\Reports\Services\SaaSykitReportsService;
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

        // Register SaaSykit compatibility services
        $this->registerSaaSykitServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Views', 'reports');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        
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
                // Commands will be registered here
            ]);
        }

        // Register SaaSykit integration
        $this->registerSaaSykitIntegration();
        
        // Register event listeners
        $this->registerEventListeners();
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
        'alpha.reports.tenant',
        'alpha.reports.subscription',
        ];
    }
}