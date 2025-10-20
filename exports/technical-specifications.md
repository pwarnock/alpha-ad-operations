# 🔧 Technical Specifications Document

## SaaSykit-Compatible Advanced Reports Package

---

## 📋 System Requirements

### **Minimum Requirements**

- **PHP**: 8.2 or higher
- **Laravel**: 11.0 or higher
- **Database**: MySQL 8.0+ or PostgreSQL 12+
- **Memory**: 512MB minimum, 2GB recommended
- **Storage**: 100MB for package, additional for data

### **Recommended Requirements**

- **PHP**: 8.3
- **Laravel**: 11.x latest
- **Database**: MySQL 8.0+ with Redis 7.0+
- **Memory**: 4GB+
- **Storage**: 1GB+ for large datasets

### **Optional Dependencies**

- **Redis**: For caching and session storage
- **Elasticsearch**: For advanced search capabilities
- **Queue Driver**: For background report processing
- **CDN**: For static asset delivery

---

## 🏗️ Architecture Overview

### **Package Structure**

```
alpha/reports/
├── src/
│   ├── Services/                    # Core business logic
│   ├── Models/                     # Eloquent models
│   ├── Http/                       # Controllers and middleware
│   ├── Filament/                   # Admin panel resources
│   ├── Views/                      # Blade templates
│   ├── Config/                     # Configuration files
│   ├── Database/                   # Migrations and seeds
│   ├── Contracts/                  # Interfaces and abstractions
│   ├── Events/                     # Event definitions
│   ├── Jobs/                       # Background jobs
│   └── ReportsServiceProvider.php   # Main service provider
├── tests/                          # Test suite
├── resources/                      # Frontend assets
└── config/                         # Package configuration
```

### **Core Components**

#### **1. ReportBuilderService**

```php
interface ReportBuilderServiceInterface
{
    // Query building
    public function buildQuery(array $filters): Builder;
    public function applyMetrics(Builder $query, array $metrics): Collection;
    public function applyGrouping(Builder $query, string $groupBy): Collection;

    // Validation
    public function validateFilters(array $filters): ValidationResult;
    public function validateMetrics(array $metrics): ValidationResult;

    // Data access
    public function getAvailableDimensions(): array;
    public function getAvailableMetrics(): array;
    public function getFilterOptions(string $dimension): array;

    // Report generation
    public function generateReport(SavedReport $report): ReportData;
    public function generateAsync(SavedReport $report): string; // Job ID
}
```

#### **2. ChartDataService**

```php
class ChartDataService
{
    // Chart transformations
    public function transformForLineChart(Collection $data, array $options): array;
    public function transformForBarChart(Collection $data, array $options): array;
    public function transformForPieChart(Collection $data, array $options): array;
    public function transformForAreaChart(Collection $data, array $options): array;

    // Data formatting
    public function formatTimeSeries(Collection $data, string $period): array;
    public function formatCategorical(Collection $data, string $category): array;
    public function formatComparison(Collection $data, string $baseline): array;

    // Chart configuration
    public function getChartConfig(string $type): array;
    public function getChartOptions(string $type, array $data): array;
}
```

#### **3. SaaSykitReportsService**

```php
class SaaSykitReportsService implements SaaSykitCompatible
{
    // Tenant management
    public function getCurrentTenant(): ?Tenant;
    public function userBelongsToTenant(User $user, Tenant $tenant): bool;
    public function getTenantUsers(Tenant $tenant): Collection;

    // Subscription management
    public function getActiveSubscription(Tenant $tenant): ?Subscription;
    public function canCreateReport(Tenant $tenant): bool;
    public function getReportLimit(Tenant $tenant): int;
    public function getAvailableFeatures(Tenant $tenant): array;

    // Feature access
    public function canAccessFeature(Tenant $tenant, string $feature): bool;
    public function trackUsage(Tenant $tenant, string $feature, int $units = 1): void;
    public function getUsageStats(Tenant $tenant, string $period): array;
}
```

---

## 🗄️ Database Schema

### **Core Tables**

#### **Reports Table**

```sql
CREATE TABLE reports (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    report_type ENUM('advertiser_performance', 'campaign_delivery', 'inventory', 'revenue', 'custom') NOT NULL,
    configuration JSON NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    is_scheduled BOOLEAN DEFAULT FALSE,
    schedule_config JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_reports_tenant (tenant_id),
    INDEX idx_reports_user (user_id),
    INDEX idx_reports_type (report_type),
    INDEX idx_reports_public (is_public),
    INDEX idx_reports_scheduled (is_scheduled),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### **Report Subscriptions Table**

```sql
CREATE TABLE report_subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    notification_preferences JSON,
    last_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_subscription (report_id, user_id),
    INDEX idx_subscriptions_report (report_id),
    INDEX idx_subscriptions_user (user_id),
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### **Report Cache Table**

```sql
CREATE TABLE report_cache (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    report_id BIGINT NOT NULL,
    cache_key VARCHAR(255) NOT NULL UNIQUE,
    data JSON NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_cache_report (report_id),
    INDEX idx_cache_expires (expires_at),
    INDEX idx_cache_key (cache_key),
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
);
```

### **Indexes for Performance**

```sql
-- Core reporting indexes
CREATE INDEX idx_impressions_tenant_date ON impressions(tenant_id, date);
CREATE INDEX idx_impressions_campaign_date ON impressions(campaign_id, date);
CREATE INDEX idx_impressions_line_item_date ON impressions(line_item_id, date);
CREATE INDEX idx_impressions_advertiser_date ON impressions(advertiser_id, date);

-- Filter-specific indexes
CREATE INDEX idx_impressions_country_device ON impressions(country, device);
CREATE INDEX idx_impressions_browser ON impressions(browser);
CREATE INDEX idx_impressions_ad_size ON impressions(ad_size);

-- Composite indexes for common queries
CREATE INDEX idx_impressions_tenant_campaign_date ON impressions(tenant_id, campaign_id, date);
CREATE INDEX idx_impressions_tenant_line_item_date ON impressions(tenant_id, line_item_id, date);
CREATE INDEX idx_impressions_tenant_country_date ON impressions(tenant_id, country, date);
```

---

## 🔌 API Specifications

### **REST API Endpoints**

#### **Report Management**

```php
// GET /api/reports - List reports
Route::get('/api/reports', [ReportApiController::class, 'index'])
    ->middleware(['auth', 'tenant.scope']);

// POST /api/reports - Create report
Route::post('/api/reports', [ReportApiController::class, 'store'])
    ->middleware(['auth', 'tenant.scope', 'feature:reports.create']);

// GET /api/reports/{id} - Get report
Route::get('/api/reports/{id}', [ReportApiController::class, 'show'])
    ->middleware(['auth', 'tenant.scope']);

// PUT /api/reports/{id} - Update report
Route::put('/api/reports/{id}', [ReportApiController::class, 'update'])
    ->middleware(['auth', 'tenant.scope']);

// DELETE /api/reports/{id} - Delete report
Route::delete('/api/reports/{id}', [ReportApiController::class, 'destroy'])
    ->middleware(['auth', 'tenant.scope']);
```

#### **Report Generation**

```php
// POST /api/reports/{id}/generate - Generate report
Route::post('/api/reports/{id}/generate', [ReportApiController::class, 'generate'])
    ->middleware(['auth', 'tenant.scope', 'feature:reports.generate']);

// GET /api/reports/{id}/data - Get report data
Route::get('/api/reports/{id}/data', [ReportApiController::class, 'data'])
    ->middleware(['auth', 'tenant.scope']);

// GET /api/reports/{id}/export/{format} - Export report
Route::get('/api/reports/{id}/export/{format}', [ReportApiController::class, 'export'])
    ->middleware(['auth', 'tenant.scope', 'feature:reports.export']);
```

#### **Filter Options**

```php
// GET /api/reports/filters/{dimension} - Get filter options
Route::get('/api/reports/filters/{dimension}', [ReportApiController::class, 'filterOptions'])
    ->middleware(['auth', 'tenant.scope']);

// GET /api/reports/metrics - Get available metrics
Route::get('/api/reports/metrics', [ReportApiController::class, 'metrics'])
    ->middleware(['auth', 'tenant.scope']);
```

### **API Response Format**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Campaign Performance Report",
        "report_type": "campaign_delivery",
        "configuration": {
            "filters": {
                "date_range": {
                    "start": "2025-10-01",
                    "end": "2025-10-31"
                },
                "campaigns": [1, 2, 3],
                "metrics": ["impressions", "clicks", "revenue", "ctr"]
            },
            "grouping": "day"
        },
        "data": [
            {
                "period": "2025-10-01",
                "impressions": 15000,
                "clicks": 450,
                "revenue": 125.50,
                "ctr": 3.0
            }
        ],
        "charts": {
            "line": {
                "data": [...],
                "options": {...}
            }
        }
    },
    "meta": {
        "total_rows": 31,
        "generated_at": "2025-10-20T12:00:00Z",
        "cache_ttl": 3600
    }
}
```

---

## 🔐 Security Implementation

### **Multi-Tenant Isolation**

#### **Database Level Security**

```php
// Global scope for tenant isolation
trait TenantScoped
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

// Middleware for tenant resolution
class TenantScopeMiddleware
{
    public function handle($request, Closure $next)
    {
        $tenant = $this->resolveTenant($request);

        if (!$tenant) {
            abort(403, 'Tenant not found or access denied');
        }

        app()->instance('current_tenant', $tenant);

        // Set tenant context on database connection
        DB::statement("SET @current_tenant_id = {$tenant->id}");

        return $next($request);
    }
}
```

#### **Row-Level Security**

```sql
-- Create view for tenant-scoped access
CREATE VIEW tenant_reports AS
SELECT * FROM reports
WHERE tenant_id = @current_tenant_id;

-- Application-level checks
CREATE TRIGGER check_report_tenant
BEFORE INSERT ON reports
FOR EACH ROW
BEGIN
    IF NEW.tenant_id != @current_tenant_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tenant access violation';
    END IF;
END;
```

### **Subscription-Based Access Control**

#### **Feature Gates**

```php
class FeatureGate
{
    public function allows(Tenant $tenant, string $feature): bool
    {
        $subscription = $tenant->subscription;

        if (!$subscription || !$subscription->active) {
            return false;
        }

        return match ($feature) {
            'reports.create' => true,
            'reports.advanced_filters' => $subscription->plan->tier >= 'professional',
            'reports.charts' => $subscription->plan->tier >= 'professional',
            'reports.api_access' => $subscription->plan->tier >= 'professional',
            'reports.unlimited' => $subscription->plan->tier === 'enterprise',
            'reports.white_label' => $subscription->plan->tier === 'enterprise',
            default => false,
        };
    }

    public function checkLimit(Tenant $tenant, string $resource): bool
    {
        $subscription = $tenant->subscription;
        $current = $this->getCurrentUsage($tenant, $resource);
        $limit = $subscription->plan->limits[$resource] ?? 0;

        return $current < $limit;
    }
}
```

#### **Middleware Implementation**

```php
class FeatureMiddleware
{
    public function handle($request, Closure $next, string $feature)
    {
        $tenant = app('current_tenant');
        $gate = app(FeatureGate::class);

        if (!$gate->allows($tenant, $feature)) {
            abort(403, 'Feature not available in your subscription plan');
        }

        return $next($request);
    }
}
```

---

## ⚡ Performance Optimization

### **Caching Strategy**

#### **Multi-Level Cache**

```php
class ReportCacheManager
{
    public function remember(string $key, callable $callback, int $ttl = 3600): mixed
    {
        // L1: Application cache (Redis)
        $value = Cache::remember($key, $ttl, $callback);

        // L2: Database cache table
        $this->storeInDatabase($key, $value, $ttl);

        return $value;
    }

    public function getReportData(SavedReport $report, array $filters): array
    {
        $cacheKey = $this->generateCacheKey($report, $filters);

        return $this->remember($cacheKey, function () use ($report, $filters) {
            return $this->generateFreshData($report, $filters);
        }, $this->getCacheTTL($report));
    }
}
```

#### **Cache Key Strategy**

```php
class CacheKeyGenerator
{
    public function generateReportKey(SavedReport $report, array $filters): string
    {
        $components = [
            'report' => $report->id,
            'tenant' => $report->tenant_id,
            'filters' => md5(json_encode($filters, JSON_SORT_KEYS)),
            'version' => $this->getDataVersion($report),
        ];

        return 'reports:' . implode(':', $components);
    }

    public function generateFilterOptionsKey(string $dimension, int $tenantId): string
    {
        return "filters:{$tenantId}:{$dimension}";
    }
}
```

### **Query Optimization**

#### **Efficient Aggregations**

```php
class OptimizedQueryBuilder
{
    public function buildMetricsQuery(Builder $query, array $metrics): Builder
    {
        $selects = ['id'];

        foreach ($metrics as $metric) {
            $selects[] = match ($metric) {
                'impressions' => DB::raw('SUM(impressions) as impressions'),
                'clicks' => DB::raw('SUM(clicks) as clicks'),
                'revenue' => DB::raw('SUM(revenue) as revenue'),
                'ctr' => DB::raw('(SUM(clicks) / NULLIF(SUM(impressions), 0)) * 100 as ctr'),
                'ecpm' => DB::raw('(SUM(revenue) / NULLIF(SUM(impressions), 0)) * 1000 as ecpm'),
                'cpc' => DB::raw('(SUM(revenue) / NULLIF(SUM(clicks), 0)) as cpc'),
                default => null,
            };
        }

        return $query->select(array_filter($selects));
    }

    public function applyDateRange(Builder $query, array $dateRange): Builder
    {
        return $query->whereBetween('date', [
            $dateRange['start'],
            $dateRange['end']
        ]);
    }
}
```

### **Background Processing**

#### **Queue Configuration**

```php
class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    public function __construct(
        private SavedReport $report,
        private array $filters,
        private string $userId
    ) {}

    public function handle(ReportBuilderService $builder): void
    {
        try {
            $data = $builder->generateReport($this->report, $this->filters);

            // Store results
            $this->storeResults($data);

            // Notify user
            $this->notifyUser();

        } catch (Exception $e) {
            $this->fail($e);
        }
    }
}
```

---

## 🧪 Testing Framework

### **Test Structure**

```php
abstract class ReportsTestCase extends Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->withFactories(__DIR__.'/../database/factories');

        // Set up test tenant
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create();
        $this->tenant->users()->attach($this->user);

        app()->instance('current_tenant', $this->tenant);
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Alpha\Reports\ReportsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('reports.cache_ttl', 60);
        $app['config']->set('reports.max_reports_per_tenant', 10);
    }
}
```

### **Performance Testing**

```php
class PerformanceTest extends ReportsTestCase
{
    /** @test */
    public function it_handles_large_datasets_efficiently()
    {
        // Create 100k impression records
        Impression::factory()->count(100000)->create([
            'tenant_id' => $this->tenant->id
        ]);

        $startTime = microtime(true);

        $report = SavedReport::factory()->create([
            'tenant_id' => $this->tenant->id
        ]);

        $data = $this->builder->generateReport($report);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Report generation should complete in under 2 seconds');
        $this->assertNotEmpty($data);
    }

    /** @test */
    public function it_concurrent_requests_are_handled_correctly()
    {
        $report = SavedReport::factory()->create([
            'tenant_id' => $this->tenant->id
        ]);

        // Simulate 10 concurrent requests
        $promises = [];
        for ($i = 0; $i < 10; $i++) {
            $promises[] = $this->async(function () use ($report) {
                return $this->builder->generateReport($report);
            });
        }

        $results = Promise::settle($promises)->wait();

        foreach ($results as $result) {
            $this->assertTrue($result['state'] === 'fulfilled');
            $this->assertNotEmpty($result['value']);
        }
    }
}
```

---

## 📦 Package Configuration

### **Configuration File**

```php
// config/reports.php
return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Reports Per Tenant
    |--------------------------------------------------------------------------
    |
    | The maximum number of reports a tenant can create. This can be
    | overridden by subscription plans.
    |
    */
    'max_reports_per_tenant' => 50,

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for report data and filter options.
    |
    */
    'cache' => [
        'ttl' => env('REPORTS_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'reports',
        'driver' => env('REPORTS_CACHE_DRIVER', 'redis'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for report export functionality.
    |
    */
    'export' => [
        'pdf' => [
            'engine' => 'spatie',
            'orientation' => 'landscape',
            'format' => 'A4',
        ],
        'excel' => [
            'engine' => 'maatwebsite',
            'include_charts' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Chart Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for chart visualization.
    |
    */
    'charts' => [
        'default_type' => 'line',
        'colors' => [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
        ],
        'responsive' => true,
        'animation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable features based on subscription plans.
    |
    */
    'features' => [
        'advanced_filters' => 'professional',
        'chart_visualization' => 'professional',
        'api_access' => 'professional',
        'scheduled_reports' => 'enterprise',
        'white_label' => 'enterprise',
    ],
];
```

---

## 🚀 Deployment Guide

### **Installation Steps**

```bash
# 1. Install package
composer require alpha/reports

# 2. Publish configuration
php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider" --tag="reports-config"

# 3. Run migrations
php artisan migrate

# 4. Publish assets (optional)
php artisan vendor:publish --provider="Alpha\\Reports\\ReportsServiceProvider" --tag="reports-assets" --force

# 5. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Environment Variables**

```env
# Cache Configuration
REPORTS_CACHE_DRIVER=redis
REPORTS_CACHE_TTL=3600

# Export Configuration
REPORTS_PDF_ENGINE=spatie
REPORTS_EXCEL_ENGINE=maatwebsite

# Queue Configuration
REPORTS_QUEUE_CONNECTION=redis
REPORTS_QUEUE_NAME=reports

# Feature Flags
REPORTS_ENABLE_ADVANCED_FILTERS=true
REPORTS_ENABLE_CHARTS=true
REPORTS_ENABLE_API=true
```

### **Performance Tuning**

```php
// config/queue.php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],

// config/database.php
'redis' => [
    'client' => 'phpredis',
    'options' => [
        'cluster' => 'redis',
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
],
```

---

_This technical specification provides comprehensive details for implementing, configuring, and deploying the Advanced Reports package in SaaSykit applications._
