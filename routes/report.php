<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::middleware('auth')->group(function () {
    Route::get('/report/table', [ReportController::class, 'index'])
        ->name('report.table');

    Route::get('/report/data', [ReportController::class, 'data'])
        ->name('report.data');

    Route::get('/report/chart-data', [ReportController::class, 'chartData'])
        ->name('report.chart.data');
});
