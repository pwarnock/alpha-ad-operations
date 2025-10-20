# Service Provider Architecture

## ReportsServiceProvider Design

### Overview

The `ReportsServiceProvider` is the main integration point between the package and Laravel applications, providing auto-discovery, configuration, and SaaSykit compatibility.

### Core Responsibilities

1. **Package Registration**: Auto-discovery and service container binding
2. **Configuration Management**: Load and merge package configuration
3. **Resource Publishing**: Views, config, migrations, and assets
4. **SaaSykit Integration**: Tenant resolution and subscription hooks
5. **Command Registration**: Artisan commands for package management
6. **Event Listeners**: Package-specific event handling

### Service Provider Structure

```php
<?php
namespace Alpha\Reports;

use Illuminate\Support\ServiceProvider;
use Alpha\Reports\Services\ReportBuilderService;
use Alpha\Reports\Services\ChartDataService;
use Alpha\Reports\Services\SaaSykitReportsService;

class ReportsServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Config/reports.php', 'reports');

        $this->app->singleton(ReportBuilderService::class);
        $this->app->singleton(ChartDataService::class);
        $this->app->singleton(SaaSykitReportsService::class);
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Views', 'reports');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->publishes([
            __DIR__.'/Config/reports.php' => config_path('reports.php'),
        ], 'reports-config');

        $this->publishes([
            __DIR__.'/Views' => resource_path('views/vendor/reports'),
        ], 'reports-views');

        $this->publishes([
            __DIR__.'/Database/Migrations' => database_path('migrations'),
        ], 'reports-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                // Package commands
            ]);
        }

        $this->registerSaaSykitIntegration();
        $this->registerFilamentResources();
        $this->registerEventListeners();
    }
}
```

### Service Container Bindings

#### Core Services

```php
// Report Builder Service
$this->app->singleton(ReportBuilderService::class, function ($app) {
    return new ReportBuilderService(
        $app['db'],
        $app['cache'],
        config('reports')
    );
});

// Chart Data Service
$this->app->singleton(ChartDataService::class, function ($app) {
    return new ChartDataService(
        $app['cache'],
        config('reports.charts')
    );
});

// SaaSykit Compatibility Service
$this->app->singleton(SaaSykitReportsService::class, function ($app) {
    return new SaaSykitReportsService(
        $app['tenant.manager'] ?? null,
        $app['subscription.manager'] ?? null
    );
});
```

### Configuration Management

#### Default Configuration

```php
// src/Config/reports.php
return [
    'max_reports_per_tenant' => 50,
    'enable_real_time_updates' => true,
    'default_chart_type' => 'line',
    'export_formats' => ['pdf', 'excel'],
    'cache_ttl' => 3600,
    'charts' => [
        'library' => 'chart.js',
        'theme' => 'default',
        'responsive' => true,
    ],
    'saasykit' => [
        'tenant_isolation' => true,
        'subscription_limits' => true,
        'event_integration' => true,
    ],
];
```

#### Configuration Merging

```php
protected function mergeConfig()
{
    $this->mergeConfigFrom(
        __DIR__.'/Config/reports.php',
        'reports'
    );
}
```

### Resource Publishing

#### Configuration Publishing

```php
$this->publishes([
    __DIR__.'/Config/reports.php' => config_path('reports.php'),
], 'reports-config');
```

#### View Publishing

```php
$this->publishes([
    __DIR__.'/Views' => resource_path('views/vendor/reports'),
], 'reports-views');
```

#### Migration Publishing

```php
$this->publishes([
    __DIR__.'/Database/Migrations' => database_path('migrations'),
], 'reports-migrations');
```

#### Asset Publishing

```php
$this->publishes([
    __DIR__.'/resources/js' => public_path('vendor/reports/js'),
    __DIR__.'/resources/css' => public_path('vendor/reports/css'),
], 'reports-assets');
```

### SaaSykit Integration Points

#### Tenant Resolution

```php
protected function registerSaaSykitIntegration()
{
    if (class_exists('\SaaSykit\Tenant\TenantManager')) {
        $this->app->resolving(TenantManager::class, function ($tenantManager) {
            // Register tenant-specific report configurations
        });
    }
}
```

#### Subscription Controls

```php
protected function registerSubscriptionLimits()
{
    if (class_exists('\SaaSykit\Subscription\SubscriptionManager')) {
        $this->app->resolving(SubscriptionManager::class, function ($subscriptionManager) {
            // Register report feature limits
        });
    }
}
```

### Filament Resource Registration

#### Auto-Discovery

```php
protected function registerFilamentResources()
{
    if (class_exists('Filament\FilamentServiceProvider')) {
        // Auto-register Filament resources
        $this->app->booted(function () {
            foreach ($this->getFilamentResources() as $resource) {
                $resource::register();
            }
        });
    }
}

protected function getFilamentResources(): array
{
    return [
        \Alpha\Reports\Filament\Resources\SavedReportResource::class,
    ];
}
```

### Event System Integration

#### Event Listeners

```php
protected function registerEventListeners()
{
    $this->app['events']->listen('tenant.resolved', function ($tenant) {
        // Handle tenant resolution for reports
    });

    $this->app['events']->listen('subscription.updated', function ($subscription) {
        // Handle subscription changes affecting reports
    });
}
```

### Command Registration

#### Artisan Commands

```php
protected $commands = [
    \Alpha\Reports\Commands\InstallReports::class,
    \Alpha\Reports\Commands\GenerateReport::class,
    \Alpha\Reports\Commands\ClearReportCache::class,
];
```

### Performance Optimizations

#### Lazy Loading

```php
public function provides(): array
{
    return [
        ReportBuilderService::class,
        ChartDataService::class,
        SaaSykitReportsService::class,
    ];
}
```

#### Conditional Loading

```php
protected function registerConditionalServices()
{
    if ($this->app->environment('testing')) {
        $this->app->singleton(ReportBuilderService::class, function () {
            return new MockReportBuilderService();
        });
    }
}
```

### Error Handling

#### Graceful Degradation

```php
protected function registerWithFallbacks()
{
    try {
        $this->registerSaaSykitIntegration();
    } catch (\Exception $e) {
        logger()->warning('SaaSykit integration failed', [
            'error' => $e->getMessage(),
            'package' => 'alpha/reports'
        ]);
    }
}
```

## Integration Benefits

1. **Auto-Discovery**: No manual configuration required
2. **SaaSykit Compatibility**: Seamless tenant and subscription integration
3. **Flexible Configuration**: Overrideable defaults
4. **Resource Management**: Easy publishing and customization
5. **Performance**: Lazy loading and conditional services
6. **Extensibility**: Hook system for custom integrations
7. **Testing Support**: Mock services for testing environment
