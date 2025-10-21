<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Alpha Reports Example Configuration
    |--------------------------------------------------------------------------
    |
    | This is an example configuration file showing all available options
    | for the Alpha Reports package. Copy this to config/reports.php and
    | customize according to your needs.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Environment Detection
    |--------------------------------------------------------------------------
    |
    | Control how the package detects and integrates with SaaSykit.
    |
    */

    'environment' => [
        'auto_detect_saasykit' => true,
        'standalone_mode' => false,
        'debug_mode' => env('APP_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable features. These can be overridden by subscription features.
    |
    */

    'features' => [
        'basic_reports' => true,
        'advanced_filters' => false,
        'chart_visualization' => false,
        'excel_export' => false,
        'csv_export' => true,
        'pdf_export' => false,
        'api_access' => false,
        'real_time_updates' => false,
        'scheduled_reports' => false,
        'report_sharing' => false,
        'custom_themes' => false,
        'data_drilldown' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Configuration
    |--------------------------------------------------------------------------
    |
    | Multi-tenant settings and limits.
    |
    */

    'tenant' => [
        'max_reports_per_tenant' => 50,
        'max_scheduled_reports_per_tenant' => 10,
        'enforce_limits' => true,
        'isolation_mode' => 'strict', // strict, relaxed, disabled
        'tenant_aware_cache' => true,
        'cross_tenant_sharing' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Performance tuning options.
    |
    */

    'performance' => [
        'cache' => [
            'default_ttl' => 3600,
            'query_ttl' => 1800,
            'chart_ttl' => 900,
            'usage_ttl' => 86400,
            'tags' => ['alpha-reports'],
            'prefix' => 'reports',
        ],
        
        'query' => [
            'timeout' => 30,
            'max_rows' => 100000,
            'chunk_size' => 1000,
            'use_read_replica' => false,
            'optimize_large_queries' => true,
        ],

        'background' => [
            'queue_name' => 'reports',
            'max_attempts' => 3,
            'retry_delay' => 60,
            'large_report_threshold' => 1000,
            'concurrent_jobs' => 3,
        ],

        'memory' => [
            'limit' => '512M',
            'gc_collection' => true,
            'optimize_exports' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Export format settings and limitations.
    |
    */

    'export' => [
        'formats' => [
            'enabled' => ['csv'], // ['pdf', 'excel', 'csv', 'json']
            'default' => 'csv',
        ],
        
        'limits' => [
            'max_rows_excel' => 10000,
            'max_rows_csv' => 50000,
            'max_rows_pdf' => 5000,
            'max_file_size' => '10MB',
        ],
        
        'storage' => [
            'disk' => 'local',
            'path' => 'exports/reports',
            'retention_days' => 7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Chart Configuration
    |--------------------------------------------------------------------------
    |
    | Chart visualization settings.
    |
    */

    'charts' => [
        'default_type' => 'line',
        'library' => 'chartjs',
        'themes' => [
            'default' => 'light',
            'available' => ['light', 'dark', 'blue', 'green'],
        ],
        'max_data_points' => 1000,
        'animation_enabled' => true,
        'responsive' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | API access and rate limiting settings.
    |
    */

    'api' => [
        'enabled' => false,
        'version' => 'v1',
        'rate_limit' => '60:1',
        'auth_required' => true,
        'cors_enabled' => false,
        'pagination' => [
            'default_per_page' => 20,
            'max_per_page' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Interface Configuration
    |--------------------------------------------------------------------------
    |
    | UI/UX settings.
    |
    */

    'ui' => [
        'date_formats' => [
            'display' => 'M j, Y',
            'api' => 'Y-m-d',
            'database' => 'Y-m-d H:i:s',
            'picker' => 'Y-m-d',
        ],
        
        'timezone' => config('app.timezone', 'UTC'),
        
        'pagination' => [
            'per_page' => 25,
            'per_page_options' => [10, 25, 50, 100],
        ],
        
        'theme' => [
            'default' => 'light',
            'allow_user_theme' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SaaSykit Integration
    |--------------------------------------------------------------------------
    |
    | SaaSykit framework integration settings.
    |
    */

    'saasykit' => [
        'enabled' => true,
        'auto_detect' => true,
        
        'tenant' => [
            'model' => 'App\\Models\\Tenant',
            'foreign_key' => 'tenant_id',
            'auto_scope' => true,
        ],
        
        'user' => [
            'model' => 'App\\Models\\User',
            'foreign_key' => 'user_id',
        ],
        
        'subscription' => [
            'feature_prefix' => 'reports',
            'grace_period_days' => 3,
            'track_usage' => true,
            'usage_retention_days' => 30,
        ],
        
        'events' => [
            'enabled' => true,
            'prefix' => 'reports',
        ],
        
        'permissions' => [
            'auto_register' => true,
            'gate_prefix' => 'reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging and Monitoring
    |--------------------------------------------------------------------------
    |
    | Logging and monitoring settings.
    |
    */

    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'channel' => 'default',
        'track_performance' => false,
        'track_usage' => true,
        'track_errors' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings.
    |
    */

    'security' => [
        'encrypt_exports' => false,
        'sanitize_inputs' => true,
        'validate_queries' => true,
        'rate_limit_exports' => true,
        'max_export_per_hour' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    |
    | Development-specific settings.
    |
    */

    'development' => [
        'debug_queries' => false,
        'debug_cache' => false,
        'mock_data' => false,
        'seed_examples' => false,
        'profiling' => false,
    ],
];