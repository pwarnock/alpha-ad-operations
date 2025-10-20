# 🏛️ Reports Package Architecture

## Overview

This document details the technical architecture of the SaaSykit-compatible Advanced Reports package, including design patterns, component relationships, and integration points.

---

## 📦 Package Structure

### **Namespace Organization**

```
Alpha/Reports/
├── src/
│   ├── Services/
│   │   ├── ReportBuilderService.php      # Core report logic
│   │   ├── ChartDataService.php         # Data transformation
│   │   └── SaaSykitReportsService.php   # SaaSykit compatibility
│   ├── Models/
│   │   └── SavedReport.php              # Package model
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ReportController.php     # Web endpoints
│   │   └── Middleware/
│   │       └── TenantScope.php         # Tenant isolation
│   ├── Filament/
│   │   └── Resources/
│   │       └── SavedReportResource.php  # Admin interface
│   ├── Views/
│   │   ├── components/
│   │   │   ├── report-builder.blade.php
│   │   │   └── report-chart.blade.php
│   │   └── reports/
│   │       └── show.blade.php
│   ├── Config/
│   │   └── reports.php                 # Package configuration
│   ├── Database/
│   │   └── Migrations/
│   │       └── create_reports_tables.php
│   ├── Contracts/
│   │   └── ReportBuilderServiceInterface.php
│   ├── Events/
│   │   ├── ReportGenerated.php
│   │   └── ReportShared.php
│   ├── Jobs/
│   │   └── GenerateReportJob.php
│   └── ReportsServiceProvider.php        # Main service provider
├── tests/
│   ├── Unit/
│   │   ├── Services/
│   │   ├── Models/
│   │   └── Controllers/
│   └── Feature/
│       ├── ReportGenerationTest.php
│       └── SaaSykitIntegrationTest.php
├── resources/
│   ├── js/
│   │   ├── report-builder.js
│   │   └── report-charts.js
│   ├── css/
│   │   └── reports.css
│   └── views/ (published to Laravel)
└── config/
    └── reports.php (published to Laravel)
```

---

## 🎯 Core Components

### **1. ReportBuilderService**

**Purpose**: Central service for building and executing reports

**Responsibilities**:

- Parse and validate filter configurations
- Build optimized database queries
- Apply tenant scoping and security
- Calculate metrics and aggregations
- Handle caching strategies

**Key Methods**:

```php
class ReportBuilderService implements ReportBuilderServiceInterface
{
    public function buildQuery(array $filters): Builder;
    public function applyMetrics(Builder $query, array $metrics): Collection;
    public function applyGrouping(Builder $query, string $groupBy): Collection;
    public function validateFilters(array $filters): ValidationResult;
    public function getAvailableDimensions(): array;
    public function getAvailableMetrics(): array;
    public function generateReport(SavedReport $report): ReportData;
}
```

### **2. ChartDataService**

**Purpose**: Transform report data for visualization

**Responsibilities**:

- Convert query results to chart-friendly formats
- Support multiple chart types (line, bar, pie)
- Handle time series vs categorical data
- Apply chart-specific transformations

**Key Methods**:

```php
class ChartDataService
{
    public function transformForLineChart(Collection $data): array;
    public function transformForBarChart(Collection $data): array;
    public function transformForPieChart(Collection $data): array;
    public function getTimeSeriesData(Collection $data, string $period): array;
    public function getCategoricalData(Collection $data, string $category): array;
}
```

### **3. SaaSykitReportsService**

**Purpose**: Bridge between reports package and SaaSykit framework

**Responsibilities**:

- Implement SaaSykitCompatible interface
- Handle tenant-aware operations
- Enforce subscription limits
- Fire SaaSykit events

**Key Methods**:

```php
class SaaSykitReportsService implements SaaSykitCompatible
{
    public function getCurrentTenant(): ?Tenant;
    public function userBelongsToTenant(User $user, Tenant $tenant): bool;
    public function getActiveSubscription(Tenant $tenant): ?Subscription;
    public function canCreateReport(Tenant $tenant): bool;
    public function getReportLimit(Tenant $tenant): int;
    public function getAvailableFeatures(Tenant $tenant): array;
    public function trackReportUsage(Tenant $tenant, string $reportType): void;
}
```

---

## 🔄 Data Flow Architecture

### **Report Generation Flow**

```
1. User Request (Filament UI)
   ↓
2. ReportController::generate()
   ↓
3. SaaSykitReportsService::canCreateReport() [Permission Check]
   ↓
4. ReportBuilderService::generateReport()
   ├── validateFilters()
   ├── buildQuery() [Tenant Scoped]
   ├── applyMetrics()
   └── applyGrouping()
   ↓
5. ChartDataService::transform() [If charts requested]
   ↓
6. Cache::remember() [Performance optimization]
   ↓
7. Response (JSON/View)
```

### **Data Access Pattern**

```
Tenant Request
    ↓
TenantScope Middleware
    ↓
ReportBuilderService
    ↓
Query Builder + Tenant Constraints
    ↓
Database (Tenant Isolated)
    ↓
Results Processing
    ↓
Response (Tenant Scoped)
```

---

## 🔐 Security Architecture

### **Multi-Tenant Isolation**

#### **Database Level**

```sql
-- All queries automatically include tenant_id
WHERE tenant_id = :current_tenant_id

-- Row-level security through model scopes
class SavedReport extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query) {
            $tenant = app('current_tenant');
            if ($tenant) {
                $query->where('tenant_id', $tenant->id);
            }
        });
    }
}
```

#### **Application Level**

```php
// TenantScope middleware
class TenantScope
{
    public function handle($request, Closure $next)
    {
        $tenant = $this->resolveTenant($request);
        app()->instance('current_tenant', $tenant);

        // Set tenant on database connection
        if ($tenant) {
            DB::statement("SET app.tenant_id = {$tenant->id}");
        }

        return $next($request);
    }
}
```

### **Subscription-Based Feature Access**

```php
class FeatureGate
{
    public function allows(Tenant $tenant, string $feature): bool
    {
        $subscription = $tenant->subscription;

        return match ($feature) {
            'advanced_filters' => $subscription->plan->tier >= 'professional',
            'chart_visualization' => $subscription->plan->tier >= 'professional',
            'api_access' => $subscription->plan->tier === 'enterprise',
            'unlimited_reports' => $subscription->plan->tier === 'enterprise',
            default => false,
        };
    }
}
```

---

## ⚡ Performance Architecture

### **Caching Strategy**

#### **Multi-Level Caching**

```
1. Application Cache (Redis)
   ├── Report results (TTL: 1 hour)
   ├── Filter options (TTL: 24 hours)
   └── User permissions (TTL: 15 minutes)

2. Database Query Cache
   ├── Aggregated metrics (TTL: 30 minutes)
   ├── Dimension values (TTL: 6 hours)
   └── Chart data (TTL: 1 hour)

3. Browser Cache
   ├── Static assets (1 week)
   ├── Report configurations (1 hour)
   └── Chart configurations (30 minutes)
```

#### **Cache Keys Pattern**

```php
// Report results cache
$cacheKey = "reports:{$tenant->id}:{$report->id}:" . md5(json_encode($filters));

// Filter options cache
$cacheKey = "reports:{$tenant->id}:filters:{$dimension}";

// Chart data cache
$cacheKey = "reports:{$tenant->id}:chart:{$report->id}:{$chartType}";
```

### **Database Optimization**

#### **Strategic Indexes**

```sql
-- Core reporting indexes
CREATE INDEX idx_impressions_tenant_date ON impressions(tenant_id, date);
CREATE INDEX idx_impressions_campaign_date ON impressions(campaign_id, date);
CREATE INDEX idx_impressions_line_item_date ON impressions(line_item_id, date);
CREATE INDEX idx_impressions_advertiser_date ON impressions(advertiser_id, date);

-- Filter-specific indexes
CREATE INDEX idx_impressions_country_device ON impressions(country, device);
CREATE INDEX idx_impressions_date_metrics ON impressions(date, impressions, clicks, revenue);

-- Composite indexes for common queries
CREATE INDEX idx_impressions_tenant_campaign_date ON impressions(tenant_id, campaign_id, date);
CREATE INDEX idx_impressions_tenant_line_item_date ON impressions(tenant_id, line_item_id, date);
```

#### **Query Optimization**

```php
// Efficient aggregation with subqueries
$query = Impression::query()
    ->select([
        'campaign_id',
        DB::raw('SUM(impressions) as total_impressions'),
        DB::raw('SUM(clicks) as total_clicks'),
        DB::raw('SUM(revenue) as total_revenue'),
        DB::raw('SUM(clicks) / SUM(impressions) * 100 as ctr'),
        DB::raw('SUM(revenue) / SUM(impressions) * 1000 as ecpm'),
    ])
    ->where('tenant_id', $tenant->id)
    ->whereBetween('date', [$startDate, $endDate])
    ->groupBy('campaign_id');
```

---

## 🔌 Integration Architecture

### **SaaSykit Integration Points**

#### **Event System**

```php
// Fire SaaSykit-compatible events
Event::dispatch('saasykit.reports.generated', [
    'report' => $report,
    'tenant' => $tenant,
    'user' => $user,
]);

Event::dispatch('saasykit.reports.shared', [
    'report' => $report,
    'shared_with' => $users,
    'shared_by' => $user,
]);
```

#### **Service Container Binding**

```php
// ReportsServiceProvider
public function register()
{
    $this->app->singleton(SaaSykitReportsService::class);

    $this->app->bind(ReportBuilderServiceInterface::class, ReportBuilderService::class);

    // Register SaaSykit compatibility
    $this->app->bind(SaaSykitCompatible::class, SaaSykitReportsService::class);
}
```

#### **Middleware Registration**

```php
// Register tenant scoping middleware
$this->app['router']->aliasMiddleware('reports.tenant', TenantScope::class);

// Apply to report routes
Route::middleware(['web', 'auth', 'reports.tenant'])
    ->prefix('reports')
    ->group(function () {
        Route::get('/{report}', [ReportController::class, 'show']);
        Route::post('/{report}/export', [ReportController::class, 'export']);
    });
```

### **Filament Integration**

#### **Resource Auto-Discovery**

```php
// Auto-register Filament resources
public function boot()
{
    if (class_exists(\Filament\Filament::class)) {
        \Filament\Facades\Filament::serving(function () {
            \Filament\Facades\Filament::registerResources([
                SavedReportResource::class,
            ]);
        });
    }
}
```

#### **Panel Integration**

```php
// Add to existing panels
public function registerPanelResources()
{
    foreach (\Filament\Facades\Filament::getPanels() as $panel) {
        if ($panel->getId() === 'publisher') {
            $panel->resources([
                SavedReportResource::class,
            ]);
        }
    }
}
```

---

## 🧪 Testing Architecture

### **Test Structure**

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── ReportBuilderServiceTest.php
│   │   ├── ChartDataServiceTest.php
│   │   └── SaaSykitReportsServiceTest.php
│   ├── Models/
│   │   └── SavedReportTest.php
│   └── Http/
│       └── ReportControllerTest.php
├── Feature/
│   ├── ReportGenerationTest.php
│   ├── SaaSykitIntegrationTest.php
│   ├── TenantIsolationTest.php
│   └── FilamentIntegrationTest.php
└── Performance/
    ├── LargeDatasetTest.php
    └── ConcurrentAccessTest.php
```

### **Testbench Setup**

```php
abstract class TestCase extends Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->withFactories(__DIR__.'/../database/factories');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Alpha\Reports\ReportsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('reports.max_reports_per_tenant', 50);
    }
}
```

---

## 📦 Package Distribution

### **Composer Configuration**

```json
{
  "name": "alpha/reports",
  "description": "Advanced reporting package for SaaSykit applications",
  "type": "laravel-package",
  "keywords": ["laravel", "saasykit", "reports", "analytics", "filament"],
  "license": "MIT",
  "authors": [
    {
      "name": "Alpha Development Team",
      "email": "dev@alpha.com"
    }
  ],
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "filament/filament": "^3.0",
    "maatwebsite/excel": "^3.1",
    "spatie/laravel-pdf": "^1.5"
  },
  "require-dev": {
    "orchestra/testbench": "^9.0",
    "phpunit/phpunit": "^11.0"
  },
  "autoload": {
    "psr-4": {
      "Alpha\\Reports\\": "src/"
    }
  },
  "extra": {
    "laravel": {
      "providers": ["Alpha\\Reports\\ReportsServiceProvider"]
    }
  }
}
```

### **Auto-Discovery Configuration**

```php
// ReportsServiceProvider
public function register()
{
    // Auto-discovery registration
    $this->mergeConfigFrom(
        __DIR__.'/../config/reports.php', 'reports'
    );
}

public function boot()
{
    // Auto-register resources
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'alpha-reports');
    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    // Publishable assets
    $this->publishes([
        __DIR__.'/../config/reports.php' => config_path('reports.php'),
    ], 'reports-config');

    $this->publishes([
        __DIR__.'/../resources/views' => resource_path('views/vendor/alpha-reports'),
    ], 'reports-views');

    $this->publishes([
        __DIR__.'/../resources/js' => public_path('vendor/alpha-reports/js'),
        __DIR__.'/../resources/css' => public_path('vendor/alpha-reports/css'),
    ], 'reports-assets');
}
```

---

## 🔄 Extension Points

### **Custom Metrics**

```php
// Register custom metrics
ReportBuilderService::extendMetric('custom_metric', function ($query) {
    return $query->selectRaw('SUM(custom_field) as custom_metric');
});

// Use in reports
$report->configuration['metrics'][] = 'custom_metric';
```

### **Custom Dimensions**

```php
// Register custom dimensions
ReportBuilderService::extendDimension('custom_dimension', function ($query, $values) {
    return $query->whereIn('custom_field', $values);
});

// Use in filters
$filters['custom_dimension'] = ['value1', 'value2'];
```

### **Custom Chart Types**

```php
// Register custom chart types
ChartDataService::extendChart('custom_chart', function ($data) {
    return $this->transformForCustomChart($data);
});
```

---

_This architecture provides a solid foundation for a scalable, secure, and extensible reporting package that integrates seamlessly with SaaSykit applications._
