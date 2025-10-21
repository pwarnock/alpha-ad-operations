# 🏗️ Phase 2: Technical Architecture

## **Service Layer Design**

```
ReportBuilderService
├── QueryBuilder
├── MetricsCalculator  
├── FilterProcessor
└── DataTransformer

SaaSykitReportsService
├── TenantManager
├── SubscriptionChecker
└── PermissionHandler

ReportCacheService
├── RedisAdapter
├── QueryCache
└── CacheInvalidation
```

## **Database Schema**

```sql
-- Package-agnostic reports table
CREATE TABLE alpha_reports (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NULL, -- Optional for SaaSykit
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    configuration JSON NOT NULL,
    report_type VARCHAR(100),
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_alpha_reports_tenant (tenant_id),
    INDEX idx_alpha_reports_user (user_id),
    INDEX idx_alpha_reports_type (report_type)
);
```

## **Configuration Structure**

```php
// config/reports.php
return [
    'features' => [
        'real_time_updates' => env('REPORTS_REAL_TIME', true),
        'advanced_charts' => env('REPORTS_CHARTS', true),
        'export_pdf' => env('REPORTS_PDF', true),
        'export_excel' => env('REPORTS_EXCEL', true),
    ],
    'performance' => [
        'cache_ttl' => env('REPORTS_CACHE_TTL', 3600),
        'max_rows' => env('REPORTS_MAX_ROWS', 10000),
        'background_processing' => env('REPORTS_BACKGROUND', true),
    ],
    'saasykit' => [
        'auto_detect' => true,
        'tenant_isolation' => true,
        'subscription_limits' => true,
    ],
];
```

## **Package Dependencies**

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "filament/filament": "^3.0",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^2.0"
    },
    "suggest": {
        "saasykit/laravel": "For multi-tenant SaaS functionality",
        "saasykit/subscriptions": "For subscription-based feature limits"
    }
}
```

## **File Structure**

```
packages/reports/src/
├── Config/
│   └── reports.php
├── Contracts/
│   ├── ReportBuilderServiceInterface.php
│   └── SaaSykitCompatible.php
├── Services/
│   ├── ReportBuilderService.php
│   ├── ReportCacheService.php
│   ├── ReportFilterService.php
│   └── SaaSykitReportsService.php
├── Models/
│   └── SavedReport.php
├── Http/Controllers/
│   └── ReportController.php
├── Filament/
│   └── Resources/
│       └── SavedReportResource.php
├── Views/
│   ├── components/
│   │   ├── report-table.blade.php
│   │   ├── report-filters.blade.php
│   │   └── export-buttons.blade.php
│   └── reports/
│       └── show.blade.php
├── Database/Migrations/
│   └── create_alpha_reports_table.php
└── Resources/
    └── js/
        └── report-builder.js
```

---

*See PHASE2_IMPLEMENTATION.md for detailed implementation steps*