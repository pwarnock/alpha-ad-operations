<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

// Public marketing routes
Route::get('/', function () {
    return view('marketing.home');
})->name('home');

Route::get('/pricing', function () {
    $plans = [
        [
            'name' => 'Starter',
            'price_monthly' => 25,
            'price_annual' => 300,
            'description' => 'Perfect for small publishers just getting started with programmatic advertising.',
            'features' => [
                'Single publication',
                '1-2 users',
                '50K impressions/month',
                'Basic reporting',
                'Email support',
            ],
            'featured' => false,
        ],
        [
            'name' => 'Professional',
            'price_monthly' => 35,
            'price_annual' => 420,
            'description' => 'Ideal for growing media companies with multiple publications.',
            'features' => [
                'Multiple publications',
                '5 users',
                '500K impressions/month',
                'Advanced reporting',
                'API access',
                'Priority support',
            ],
            'featured' => true,
        ],
        [
            'name' => 'Enterprise',
            'price_monthly' => 'Custom',
            'price_annual' => 'Custom',
            'description' => 'Custom solutions for large media enterprises.',
            'features' => [
                'Unlimited publications',
                'Unlimited users',
                'Unlimited impressions',
                'White-label options',
                'Custom integrations',
                'Dedicated account manager',
                'SLA guarantees',
            ],
            'featured' => false,
        ],
    ];
    return view('marketing.pricing', ['plans' => $plans, 'billing' => 'monthly']);
})->name('pricing');

Route::get('/register', function () {
    return 'Registration page placeholder';
})->name('register');

// Report routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/{saved_report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{saved_report}/run', [ReportController::class, 'run'])->name('reports.run');
    Route::post('/reports/{saved_report}/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/{saved_report}/export/html', [ReportController::class, 'exportHtml'])->name('reports.export.html');
    Route::post('/reports/{saved_report}/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
});

// Redirect admin to publisher to avoid confusion
Route::redirect('/admin', '/publisher', 301);

// Filament admin routes (auto-registered)
// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });