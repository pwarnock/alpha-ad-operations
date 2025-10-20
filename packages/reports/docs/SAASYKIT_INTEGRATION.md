# SaaSykit Integration Points

## Overview

The `alpha/reports` package integrates seamlessly with SaaSykit's multi-tenant architecture, providing tenant isolation, subscription controls, and event compatibility.

## Core Integration Components

### 1. Tenant Isolation System

#### Automatic Tenant Scoping

```php
<?php
namespace Alpha\Reports\Services;

use SaaSykit\Tenant\TenantManager;
use Illuminate\Database\Eloquent\Builder;

class SaaSykitReportsService implements SaaSykitCompatible
{
    public function __construct(
        protected ?TenantManager $tenantManager = null
    ) {}

    public function scopeToTenant(Builder $query): Builder
    {
        if ($this->tenantManager && $tenant = $this->tenantManager->current()) {
            return $query->where('tenant_id', $tenant->id);
        }

        return $query;
    }

    public function getCurrentTenantId(): ?int
    {
        return $this->tenantManager?->current()?->id;
    }
}
```

#### Tenant-Aware Model Traits

```php
<?php
namespace Alpha\Reports\Traits;

use SaaSykit\Tenant\TenantManager;

trait TenantScoped
{
    protected static function bootTenantScoped(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $tenantManager = app(TenantManager::class);

            if ($tenantManager && $tenant = $tenantManager->current()) {
                $query->where('tenant_id', $tenant->id);
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

### 2. Subscription Controls

#### Feature Access Validation

```php
<?php
namespace Alpha\Reports\Services;

use SaaSykit\Subscription\SubscriptionManager;

class SubscriptionReportsService
{
    public function __construct(
        protected SubscriptionManager $subscriptionManager
    ) {}

    public function canCreateReports(): bool
    {
        return $this->subscriptionManager->hasFeature('reports.create');
    }

    public function canUseAdvancedFilters(): bool
    {
        return $this->subscriptionManager->hasFeature('reports.advanced_filters');
    }

    public function canExportToExcel(): bool
    {
        return $this->subscriptionManager->hasFeature('reports.excel_export');
    }

    public function getMaxReportsPerTenant(): int
    {
        return $this->subscriptionManager->getLimit('reports.max_per_tenant', 10);
    }

    public function canUseRealTimeUpdates(): bool
    {
        return $this->subscriptionManager->hasFeature('reports.real_time');
    }
}
```

#### Subscription-Based Feature Flags

```php
<?php
namespace Alpha\Reports\Middleware;

use Closure;
use Illuminate\Http\Request;
use SaaSykit\Subscription\SubscriptionManager;

class SubscriptionCheck
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $subscriptionManager = app(SubscriptionManager::class);

        if (!$subscriptionManager->hasFeature($feature)) {
            return response()->json([
                'error' => 'Feature not available in current subscription',
                'feature' => $feature,
                'required_plan' => $subscriptionManager->getRequiredPlan($feature)
            ], 403);
        }

        return $next($request);
    }
}
```

### 3. Event System Integration

#### SaaSykit-Compatible Events

```php
<?php
namespace Alpha\Reports\Events;

use SaaSykit\Events\SaaSykitEvent;
use Alpha\Reports\Models\SavedReport;

class ReportGenerated extends SaaSykitEvent
{
    public function __construct(
        public SavedReport $report,
        public array $data,
        public ?int $executionTime = null
    ) {
        parent::__construct('reports.generated', [
            'report_id' => $report->id,
            'tenant_id' => $report->tenant_id,
            'user_id' => $report->user_id,
            'report_type' => $report->report_type,
            'data_points' => count($data),
            'execution_time' => $executionTime,
        ]);
    }
}

class ReportShared extends SaaSykitEvent
{
    public function __construct(
        public SavedReport $report,
        public string $sharedWith,
        public string $shareType // 'user', 'team', 'public'
    ) {
        parent::__construct('reports.shared', [
            'report_id' => $report->id,
            'tenant_id' => $report->tenant_id,
            'shared_by' => $report->user_id,
            'shared_with' => $sharedWith,
            'share_type' => $shareType,
        ]);
    }
}
```

#### Event Listeners for SaaSykit

```php
<?php
namespace Alpha\Reports\Listeners;

use SaaSykit\Events\TenantResolved;
use SaaSykit\Events\SubscriptionUpdated;
use Alpha\Reports\Services\ReportCacheService;

class SaaSykitEventListener
{
    public function handleTenantResolved(TenantResolved $event): void
    {
        // Clear tenant-specific report cache
        app(ReportCacheService::class)->clearTenantCache($event->tenant->id);
    }

    public function handleSubscriptionUpdated(SubscriptionUpdated $event): void
    {
        // Update feature availability for tenant
        if ($event->hasFeatureChange('reports')) {
            app(ReportCacheService::class)->clearTenantCache($event->tenant->id);
        }
    }
}
```

### 4. Permission System Integration

#### SaaSykit Permission Checks

```php
<?php
namespace Alpha\Reports\Policies;

use SaaSykit\Auth\SaaSykitUser;
use Alpha\Reports\Models\SavedReport;

class SavedReportPolicy
{
    public function view(SaaSykitUser $user, SavedReport $report): bool
    {
        // Check tenant ownership
        if ($user->tenant_id !== $report->tenant_id) {
            return false;
        }

        // Check SaaSykit permissions
        return $user->hasPermission('reports.view') ||
               $report->user_id === $user->id ||
               $report->is_public;
    }

    public function create(SaaSykitUser $user): bool
    {
        return $user->hasPermission('reports.create') &&
               app(SubscriptionReportsService::class)->canCreateReports();
    }

    public function update(SaaSykitUser $user, SavedReport $report): bool
    {
        return $user->tenant_id === $report->tenant_id &&
               ($user->hasPermission('reports.update') || $report->user_id === $user->id);
    }

    public function delete(SaaSykitUser $user, SavedReport $report): bool
    {
        return $user->tenant_id === $report->tenant_id &&
               ($user->hasPermission('reports.delete') || $report->user_id === $user->id);
    }
}
```

### 5. API Integration Points

#### Tenant-Aware API Controllers

```php
<?php
namespace Alpha\Reports\Http\Controllers;

use SaaSykit\Tenant\TenantManager;
use Alpha\Reports\Services\ReportBuilderService;

class ReportController extends Controller
{
    public function __construct(
        protected ReportBuilderService $reportBuilder,
        protected TenantManager $tenantManager
    ) {}

    public function index(Request $request)
    {
        $tenant = $this->tenantManager->current();

        $reports = $this->reportBuilder
            ->forTenant($tenant->id)
            ->withFilters($request->all())
            ->get();

        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $this->authorize('create', SavedReport::class);

        $tenant = $this->tenantManager->current();

        $report = $this->reportBuilder->create([
            'tenant_id' => $tenant->id,
            'user_id' => $request->user()->id,
            // ... other fields
        ]);

        event(new ReportGenerated($report, []));

        return response()->json($report, 201);
    }
}
```

### 6. Configuration Integration

#### SaaSykit-Aware Configuration

```php
<?php
// src/Config/reports.php
return [
    'saasykit' => [
        'tenant_isolation' => env('SAASYKIT_TENANT_ISOLATION', true),
        'subscription_limits' => env('SAASYKIT_SUBSCRIPTION_LIMITS', true),
        'event_integration' => env('SAASYKIT_EVENT_INTEGRATION', true),
        'permission_integration' => env('SAASYKIT_PERMISSION_INTEGRATION', true),
    ],

    'features' => [
        'advanced_filters' => [
            'enabled' => true,
            'subscription_required' => 'professional',
        ],
        'excel_export' => [
            'enabled' => true,
            'subscription_required' => 'professional',
        ],
        'real_time_updates' => [
            'enabled' => true,
            'subscription_required' => 'enterprise',
        ],
        'api_access' => [
            'enabled' => true,
            'subscription_required' => 'enterprise',
        ],
    ],
];
```

### 7. Database Integration

#### Tenant-Aware Migrations

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // SaaSykit tenant integration
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('report_type');
            $table->json('configuration');
            $table->boolean('is_public')->default(false);

            $table->timestamps();

            // Indexes for tenant isolation
            $table->index(['tenant_id', 'report_type']);
            $table->index(['tenant_id', 'user_id']);
        });
    }
};
```

### 8. Service Registration

#### SaaSykit Service Provider Integration

```php
<?php
namespace Alpha\Reports;

use SaaSykit\Tenant\TenantManager;
use SaaSykit\Subscription\SubscriptionManager;

class ReportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register SaaSykit-compatible services
        $this->app->singleton(SaaSykitReportsService::class, function ($app) {
            return new SaaSykitReportsService(
                $app->make(TenantManager::class),
                $app->make(SubscriptionManager::class)
            );
        });

        // Register subscription service
        $this->app->singleton(SubscriptionReportsService::class, function ($app) {
            return new SubscriptionReportsService(
                $app->make(SubscriptionManager::class)
            );
        });
    }

    public function boot(): void
    {
        // Register SaaSykit event listeners
        if (class_exists('SaaSykit\Events\TenantResolved')) {
            $this->app['events']->listen(
                'SaaSykit\Events\TenantResolved',
                'Alpha\Reports\Listeners\SaaSykitEventListener@handleTenantResolved'
            );
        }
    }
}
```

## Integration Benefits

1. **Seamless Tenant Isolation**: Automatic data scoping
2. **Subscription-Based Features**: Granular feature control
3. **Event Compatibility**: SaaSykit event system integration
4. **Permission Integration**: Role-based access control
5. **Configuration Flexibility**: Environment-based settings
6. **Database Security**: Tenant-aware queries and indexes
7. **API Security**: Tenant-scoped endpoints
8. **Performance**: Optimized for multi-tenant queries

## Testing Integration

#### SaaSykit Test Helpers

```php
<?php
namespace Alpha\Reports\Tests\Helpers;

use SaaSykit\Tenant\TenantManager;
use SaaSykit\Subscription\SubscriptionManager;

trait SaaSykitTestHelpers
{
    protected function createTestTenant(array $attributes = []): Tenant
    {
        return Tenant::factory()->create($attributes);
    }

    protected function actAsTenantUser(Tenant $tenant): void
    {
        $this->actingAs($tenant->owner);
        app(TenantManager::class)->setCurrent($tenant);
    }

    protected function withSubscriptionFeatures(array $features): void
    {
        $subscriptionManager = app(SubscriptionManager::class);
        $subscriptionManager->setFeatures($features);
    }
}
```
