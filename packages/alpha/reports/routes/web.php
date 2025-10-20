<?php

use Illuminate\Support\Facades\Route;
use Alpha\Reports\Http\Controllers\ReportController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
});
