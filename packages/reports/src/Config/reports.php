<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reports Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Alpha Reports package.
    | Features include tenant isolation, subscription controls, performance
    | tuning, and environment detection for SaaSykit presence.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Environment Detection
    |--------------------------------------------------------------------------
    |
    | Automatically detect SaaSykit presence and configure accordingly.
    | Set to false to force standalone mode regardless of SaaSykit availability.
    |
    */

    'environment' => [
        'auto_detect_saasykit' => env('REPORTS_AUTO_DETECT_SAASYKIT', true),
        'standalone_mode' => env('REPORTS_STANDALONE_MODE', false),
        'debug_mode' => env('REPORTS_DEBUG', env('APP_DEBUG', false)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Control feature availability across the application. These can be
    | overridden by subscription features when SaaSykit is available.
    |
    */

    'features' => [
        'basic_reports' => env('REPORTS_FEATURE_BASIC', true),
        'advanced_filters' => env('REPORTS_FEATURE_ADVANCED_FILTERS', false),
        'chart_visualization' => env('REPORTS_FEATURE_CHARTS', false),
        'excel_export' => env('REPORTS_FEATURE_EXCEL_EXPORT', false),
        'csv_export' => env('REPORTS_FEATURE_CSV_EXPORT', true),
        'pdf_export' => env('REPORTS_FEATURE_PDF_EXPORT', false),
        'api_access' => env('REPORTS_FEATURE_API_ACCESS', false),
        'real_time_updates' => env('REPORTS_FEATURE_REAL_TIME', false),
        'scheduled_reports' => env('REPORTS_FEATURE_SCHEDULED', false),
        'report_sharing' => env('REPORTS_FEATURE_SHARING', false),
        'custom_themes' => env('REPORTS_FEATURE_THEMES', false),
        'data_drilldown' => env('REPORTS_FEATURE_DRILLDOWN', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Configuration
    |--------------------------------------------------------------------------
    |
    | Settings specific to multi-tenant environments including limits,
    | isolation, and tenant-specific behaviors.
    |
    */

    'tenant' => [
        'max_reports_per_tenant' => env('REPORTS_MAX_PER_TENANT', 50),
        'max_scheduled_reports_per_tenant' => env('REPORTS_MAX_SCHEDULED_PER_TENANT', 10),
        'enforce_limits' => env('REPORTS_ENFORCE_LIMITS', true),
        'isolation_mode' => env('REPORTS_ISOLATION_MODE', 'strict'), // strict, relaxed, disabled
        'tenant_aware_cache' => env('REPORTS_TENANT_AWARE_CACHE', true),
        'cross_tenant_sharing' => env('REPORTS_CROSS_TENANT_SHARING', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Performance tuning options for caching, queries, and background
    | processing to optimize report generation.
    |
    */

    'performance' => [
        'cache' => [
            'default_ttl' => env('REPORTS_CACHE_TTL', 3600),
            'query_ttl' => env('REPORTS_QUERY_CACHE_TTL', 1800),
            'chart_ttl' => env('REPORTS_CHART_CACHE_TTL', 900),
            'usage_ttl' => env('REPORTS_USAGE_CACHE_TTL', 86400),
            'tags' => ['alpha-reports'],
            'prefix' => env('REPORTS_CACHE_PREFIX', 'reports'),
        ],
        
        'query' => [
            'timeout' => env('REPORTS_QUERY_TIMEOUT', 30),
            'max_rows' => env('REPORTS_MAX_QUERY_ROWS', 100000),
            'chunk_size' => env('REPORTS_CHUNK_SIZE', 1000),
            'use_read_replica' => env('REPORTS_USE_READ_REPLICA', false),
            'optimize_large_queries' => env('REPORTS_OPTIMIZE_QUERIES', true),
        ],

        'background' => [
            'queue_name' => env('REPORTS_QUEUE', 'reports'),
            'max_attempts' => env('REPORTS_MAX_ATTEMPTS', 3),
            'retry_delay' => env('REPORTS_RETRY_DELAY', 60),
            'large_report_threshold' => env('REPORTS_LARGE_REPORT_THRESHOLD', 1000),
            'concurrent_jobs' => env('REPORTS_CONCURRENT_JOBS', 3),
        ],

        'memory' => [
            'limit' => env('REPORTS_MEMORY_LIMIT', '512M'),
            'gc_collection' => env('REPORTS_GC_COLLECTION', true),
            'optimize_exports' => env('REPORTS_OPTIMIZE_EXPORTS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for different export formats and their limitations.
    |
    */

    'export' => [
        'formats' => [
            'enabled' => array_filter([
                env('REPORTS_EXPORT_PDF', false) => 'pdf',
                env('REPORTS_EXPORT_EXCEL', false) => 'excel',
                env('REPORTS_EXPORT_CSV', true) => 'csv',
                env('REPORTS_EXPORT_JSON', false) => 'json',
            ]),
            'default' => env('REPORTS_DEFAULT_EXPORT_FORMAT', 'csv'),
        ],
        
        'limits' => [
            'max_rows_excel' => env('REPORTS_MAX_ROWS_EXCEL', 10000),
            'max_rows_csv' => env('REPORTS_MAX_ROWS_CSV', 50000),
            'max_rows_pdf' => env('REPORTS_MAX_ROWS_PDF', 5000),
            'max_file_size' => env('REPORTS_MAX_FILE_SIZE', '10MB'),
        ],
        
        'storage' => [
            'disk' => env('REPORTS_EXPORT_DISK', 'local'),
            'path' => env('REPORTS_EXPORT_PATH', 'exports/reports'),
            'retention_days' => env('REPORTS_EXPORT_RETENTION', 7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Chart Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for chart visualization and data presentation.
    |
    */

    'charts' => [
        'default_type' => env('REPORTS_DEFAULT_CHART_TYPE', 'line'),
        'library' => env('REPORTS_CHART_LIBRARY', 'chartjs'),
        'themes' => [
            'default' => env('REPORTS_CHART_THEME', 'light'),
            'available' => ['light', 'dark', 'blue', 'green'],
        ],
        'max_data_points' => env('REPORTS_CHART_MAX_POINTS', 1000),
        'animation_enabled' => env('REPORTS_CHART_ANIMATION', true),
        'responsive' => env('REPORTS_CHART_RESPONSIVE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for API access, rate limiting, and authentication.
    |
    */

    'api' => [
        'enabled' => env('REPORTS_API_ENABLED', false),
        'version' => env('REPORTS_API_VERSION', 'v1'),
        'rate_limit' => env('REPORTS_API_RATE_LIMIT', '60:1'),
        'auth_required' => env('REPORTS_API_AUTH_REQUIRED', true),
        'cors_enabled' => env('REPORTS_API_CORS_ENABLED', false),
        'pagination' => [
            'default_per_page' => env('REPORTS_API_PER_PAGE', 20),
            'max_per_page' => env('REPORTS_API_MAX_PER_PAGE', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Interface Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the user interface and user experience.
    |
    */

    'ui' => [
        'date_formats' => [
            'display' => env('REPORTS_DATE_DISPLAY', 'M j, Y'),
            'api' => env('REPORTS_DATE_API', 'Y-m-d'),
            'database' => env('REPORTS_DATE_DATABASE', 'Y-m-d H:i:s'),
            'picker' => env('REPORTS_DATE_PICKER', 'Y-m-d'),
        ],
        
        'timezone' => env('REPORTS_TIMEZONE', config('app.timezone', 'UTC')),
        
        'pagination' => [
            'per_page' => env('REPORTS_PER_PAGE', 25),
            'per_page_options' => [10, 25, 50, 100],
        ],
        
        'theme' => [
            'default' => env('REPORTS_UI_THEME', 'light'),
            'allow_user_theme' => env('REPORTS_ALLOW_USER_THEME', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SaaSykit Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for SaaSykit framework integration including
    | tenant management, subscription features, and event handling.
    |
    */

    'saasykit' => [
        'enabled' => env('REPORTS_SAASYKIT_ENABLED', true),
        'auto_detect' => env('REPORTS_SAASYKIT_AUTO_DETECT', true),
        
        'tenant' => [
            'model' => env('REPORTS_TENANT_MODEL', 'App\\Models\\Tenant'),
            'foreign_key' => env('REPORTS_TENANT_FOREIGN_KEY', 'tenant_id'),
            'auto_scope' => env('REPORTS_TENANT_AUTO_SCOPE', true),
        ],
        
        'user' => [
            'model' => env('REPORTS_USER_MODEL', 'App\\Models\\User'),
            'foreign_key' => env('REPORTS_USER_FOREIGN_KEY', 'user_id'),
        ],
        
        'subscription' => [
            'feature_prefix' => 'reports',
            'grace_period_days' => env('REPORTS_GRACE_PERIOD', 3),
            'track_usage' => env('REPORTS_TRACK_USAGE', true),
            'usage_retention_days' => env('REPORTS_USAGE_RETENTION', 30),
        ],
        
        'events' => [
            'enabled' => env('REPORTS_SAASYKIT_EVENTS', true),
            'prefix' => 'reports',
        ],
        
        'permissions' => [
            'auto_register' => env('REPORTS_AUTO_PERMISSIONS', true),
            'gate_prefix' => 'reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging and Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for logging, monitoring, and debugging.
    |
    */

    'logging' => [
        'enabled' => env('REPORTS_LOGGING', true),
        'level' => env('REPORTS_LOG_LEVEL', 'info'),
        'channel' => env('REPORTS_LOG_CHANNEL', 'default'),
        'track_performance' => env('REPORTS_LOG_PERFORMANCE', false),
        'track_usage' => env('REPORTS_LOG_USAGE', true),
        'track_errors' => env('REPORTS_LOG_ERRORS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for data protection and access control.
    |
    */

    'security' => [
        'encrypt_exports' => env('REPORTS_ENCRYPT_EXPORTS', false),
        'sanitize_inputs' => env('REPORTS_SANITIZE_INPUTS', true),
        'validate_queries' => env('REPORTS_VALIDATE_QUERIES', true),
        'rate_limit_exports' => env('REPORTS_RATE_LIMIT_EXPORTS', true),
        'max_export_per_hour' => env('REPORTS_MAX_EXPORT_PER_HOUR', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    |
    | Development-specific settings and debugging options.
    |
    */

    'development' => [
        'debug_queries' => env('REPORTS_DEBUG_QUERIES', false),
        'debug_cache' => env('REPORTS_DEBUG_CACHE', false),
        'mock_data' => env('REPORTS_MOCK_DATA', false),
        'seed_examples' => env('REPORTS_SEED_EXAMPLES', false),
        'profiling' => env('REPORTS_PROFILING', false),
    ],
];
