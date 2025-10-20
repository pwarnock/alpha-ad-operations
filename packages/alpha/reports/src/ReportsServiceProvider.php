<?php

namespace Alpha\Reports;

use Illuminate\Support\ServiceProvider;
use Filament\Panel;

class ReportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'reports');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/reports.php' => config_path('reports.php'),
        ], 'reports-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/reports'),
        ], 'reports-views');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'reports-migrations');
    }
}
