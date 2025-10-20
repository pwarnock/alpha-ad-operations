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

    'enabled' => env('REPORTS_ENABLED', true),

    'cache' => [
        'ttl' => env('REPORTS_CACHE_TTL', 3600), // 1 hour
    ],

    'export' => [
        'formats' => ['pdf', 'excel'],
        'max_rows' => env('REPORTS_MAX_ROWS', 10000),
    ],

    'charts' => [
        'library' => 'chartjs',
        'default_colors' => ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'],
    ],
];
