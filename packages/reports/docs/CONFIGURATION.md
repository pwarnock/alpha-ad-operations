# Alpha Reports Configuration Guide

## Overview

The Alpha Reports package features a comprehensive configuration system that automatically adapts to your environment, whether you're using SaaSykit, Laravel Tenancy, or running in standalone mode.

## Environment Detection

The package automatically detects your environment and configures itself accordingly:

### SaaSykit Detection

- Detects SaaSykit Tenant Manager and Subscription Manager
- Enables tenant isolation and subscription features
- Integrates with SaaSykit event system

### Laravel Tenancy Detection

- Detects common Laravel tenancy packages (Stancl, Spatie, Hyn)
- Enables tenant isolation without subscription features
- Provides graceful degradation

### Standalone Mode

- Runs without multi-tenancy support
- Uses configured feature flags
- Optimized for single-tenant applications

## Configuration Structure

### Environment Settings

```php
'environment' => [
    'auto_detect_saasykit' => true,  // Auto-detect SaaSykit presence
    'standalone_mode' => false,      // Force standalone mode
    'debug_mode' => env('APP_DEBUG', false),
],
```

### Feature Flags

Control feature availability across your application:

```php
'features' => [
    'basic_reports' => true,          // Core reporting functionality
    'advanced_filters' => false,      // Complex filtering capabilities
    'chart_visualization' => false,   // Chart.js integration
    'excel_export' => false,          // Excel export functionality
    'csv_export' => true,             // CSV export (always available)
    'pdf_export' => false,            // PDF export generation
    'api_access' => false,            // RESTful API endpoints
    'real_time_updates' => false,     // Real-time data updates
    'scheduled_reports' => false,     // Automated report generation
    'report_sharing' => false,        // Share reports with users
    'custom_themes' => false,         // Custom report themes
    'data_drilldown' => false,        // Drill-down into data details
],
```

### Tenant Configuration

Multi-tenant settings and limits:

```php
'tenant' => [
    'max_reports_per_tenant' => 50,           // Reports per tenant limit
    'max_scheduled_reports_per_tenant' => 10, // Scheduled reports limit
    'enforce_limits' => true,                 // Enforce tenant limits
    'isolation_mode' => 'strict',             // strict, relaxed, disabled
    'tenant_aware_cache' => true,             // Separate cache per tenant
    'cross_tenant_sharing' => false,          // Allow cross-tenant sharing
],
```

### Performance Configuration

Optimize performance based on your infrastructure:

```php
'performance' => [
    'cache' => [
        'default_ttl' => 3600,        // Default cache TTL (seconds)
        'query_ttl' => 1800,          // Query result cache TTL
        'chart_ttl' => 900,           // Chart data cache TTL
        'usage_ttl' => 86400,         // Usage tracking cache TTL
        'tags' => ['alpha-reports'],  // Cache tags for invalidation
        'prefix' => 'reports',        // Cache key prefix
    ],

    'query' => [
        'timeout' => 30,               // Query timeout (seconds)
        'max_rows' => 100000,         // Maximum rows per query
        'chunk_size' => 1000,          // Chunk size for large queries
        'use_read_replica' => false,   // Use read replica for queries
        'optimize_large_queries' => true, // Enable query optimization
    ],

    'background' => [
        'queue_name' => 'reports',     // Queue name for background jobs
        'max_attempts' => 3,           // Maximum retry attempts
        'retry_delay' => 60,           // Retry delay (seconds)
        'large_report_threshold' => 1000, // Background processing threshold
        'concurrent_jobs' => 3,        // Concurrent background jobs
    ],

    'memory' => [
        'limit' => '512M',             // Memory limit for report generation
        'gc_collection' => true,       // Enable garbage collection
        'optimize_exports' => true,    // Optimize memory during exports
    ],
],
```

### Export Configuration

Configure export formats and limitations:

```php
'export' => [
    'formats' => [
        'enabled' => ['csv'],          // Available export formats
        'default' => 'csv',            // Default export format
    ],

    'limits' => [
        'max_rows_excel' => 10000,     // Max rows for Excel export
        'max_rows_csv' => 50000,       // Max rows for CSV export
        'max_rows_pdf' => 5000,        // Max rows for PDF export
        'max_file_size' => '10MB',     // Maximum export file size
    ],

    'storage' => [
        'disk' => 'local',             // Storage disk for exports
        'path' => 'exports/reports',   // Storage path
        'retention_days' => 7,         // Export file retention
    ],
],
```

## Environment Variables

Use environment variables to override configuration:

```bash
# Environment Detection
REPORTS_AUTO_DETECT_SAASYKIT=true
REPORTS_STANDALONE_MODE=false
REPORTS_DEBUG=false

# Feature Flags
REPORTS_FEATURE_BASIC=true
REPORTS_FEATURE_ADVANCED_FILTERS=false
REPORTS_FEATURE_CHARTS=false
REPORTS_FEATURE_EXCEL_EXPORT=false
REPORTS_FEATURE_API_ACCESS=false

# Performance
REPORTS_CACHE_TTL=3600
REPORTS_QUERY_TIMEOUT=30
REPORTS_MAX_QUERY_ROWS=100000
REPORTS_QUEUE=reports

# SaaSykit Integration
REPORTS_SAASYKIT_ENABLED=true
REPORTS_TENANT_MODEL=App\\Models\\Tenant
REPORTS_USER_MODEL=App\\Models\\User
```

## Configuration Validation

Use the provided command to validate your configuration:

```bash
php artisan reports:validate-config
```

For detailed information:

```bash
php artisan reports:validate-config --detailed
```

This command will:

- Show environment information
- Display detected features
- Validate configuration settings
- Provide recommendations
- Show detailed configuration when requested

## Runtime Configuration

The package includes a configuration manager that provides runtime overrides based on:

1. **Environment Detection** - Automatically adjusts settings based on available services
2. **Feature Dependencies** - Enables dependent features automatically
3. **Performance Optimization** - Optimizes settings based on infrastructure
4. **Security Validation** - Ensures secure configurations

## Using Configuration in Code

### Accessing Configuration

```php
use Alpha\Reports\Traits\UsesReportsConfig;

class YourService
{
    use UsesReportsConfig;

    public function someMethod()
    {
        // Get configuration value
        $cacheTtl = $this->getReportsConfig('performance.cache.default_ttl', 3600);

        // Check if feature is enabled
        if ($this->isFeatureEnabled('chart_visualization')) {
            // Generate chart
        }

        // Check SaaSykit availability
        if ($this->isSaaSykitAvailable()) {
            // Use SaaSykit features
        }
    }
}
```

### Dependency Injection

```php
use Alpha\Reports\Services\ConfigurationManager;
use Alpha\Reports\Services\EnvironmentDetectionService;

class YourController
{
    public function __construct(
        private ConfigurationManager $configManager,
        private EnvironmentDetectionService $envDetection
    ) {}

    public function index()
    {
        $features = $this->configManager->getEffectiveConfig('features');
        $envInfo = $this->envDetection->getEnvironmentInfo();

        return response()->json([
            'features' => $features,
            'environment' => $envInfo,
        ]);
    }
}
```

## Configuration Recommendations

### Production Environment

```php
'environment' => [
    'debug_mode' => false,
],

'performance' => [
    'cache' => [
        'default_ttl' => 3600,
    ],
    'memory' => [
        'gc_collection' => true,
        'optimize_exports' => true,
    ],
],

'security' => [
    'sanitize_inputs' => true,
    'validate_queries' => true,
    'rate_limit_exports' => true,
],
```

### Development Environment

```php
'environment' => [
    'debug_mode' => true,
],

'development' => [
    'debug_queries' => true,
    'debug_cache' => true,
    'mock_data' => true,
    'profiling' => true,
],

'logging' => [
    'track_performance' => true,
    'level' => 'debug',
],
```

### High-Traffic Environment

```php
'performance' => [
    'cache' => [
        'default_ttl' => 7200,  // Longer cache
    ],
    'query' => [
        'use_read_replica' => true,
        'max_rows' => 50000,   // Lower limit for performance
    ],
    'background' => [
        'concurrent_jobs' => 5,  // More concurrent jobs
    ],
],
```

## Troubleshooting

### Common Issues

1. **Features not working despite being enabled**
   - Check if dependencies are met (e.g., Redis for real-time updates)
   - Run `php artisan reports:validate-config` to see issues

2. **Poor performance**
   - Enable query optimization
   - Use read replicas for large datasets
   - Increase cache TTL for frequently accessed data

3. **Memory issues with large reports**
   - Reduce `max_rows` limit
   - Enable background processing for large reports
   - Use chunking for data processing

### Debug Information

Enable debug mode to get detailed logging:

```php
'environment' => [
    'debug_mode' => true,
],

'development' => [
    'debug_queries' => true,
    'debug_cache' => true,
    'profiling' => true,
],
```

This will provide detailed logs about:

- Environment detection process
- Configuration decisions
- Performance metrics
- Cache operations
- Query execution details
