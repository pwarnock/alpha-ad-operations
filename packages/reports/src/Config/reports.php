<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reports Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Alpha Reports package.
    |
    */

    'max_reports_per_tenant' => env('REPORTS_MAX_PER_TENANT', 50),

    'cache_ttl' => env('REPORTS_CACHE_TTL', 3600),

    'default_chart_type' => env('REPORTS_DEFAULT_CHART_TYPE', 'line'),

    'export_formats' => ['pdf', 'excel', 'csv'],

    'date_formats' => [
        'display' => 'M j, Y',
        'api' => 'Y-m-d',
    ],

    'pagination' => [
        'per_page' => 25,
    ],

    /*
    |--------------------------------------------------------------------------
    | SaaSykit Integration
    |--------------------------------------------------------------------------
    */

    'saasykit' => [
        'enabled' => env('REPORTS_SAASYKIT_ENABLED', true),
        'tenant_model' => env('REPORTS_TENANT_MODEL', 'App\\Models\\Tenant'),
        'user_model' => env('REPORTS_USER_MODEL', 'App\\Models\\User'),
    ],
];
