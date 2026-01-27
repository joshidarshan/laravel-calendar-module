<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Default Report (All → Table)
|--------------------------------------------------------------------------
*/
// Route::get('/report/table', [ReportController::class, 'allTable'])
//     ->name('report.table');

/*
|--------------------------------------------------------------------------
| Day Reports
|--------------------------------------------------------------------------
*/
// Route::get('/report/day/table', [ReportController::class, 'dayTable'])
//     ->name('report.table');
Route::get('/report/day/table', [ReportController::class, 'dayTable'])
    ->name('report.day.table');
Route::get('/report/day/chart', [ReportController::class, 'dayChart'])
    ->name('report.day.chart');

/*
|--------------------------------------------------------------------------
| Week Reports
|--------------------------------------------------------------------------
*/
Route::get('/report/week/table', [ReportController::class, 'weekTable'])
    ->name('report.week.table');

Route::get('/report/week/chart', [ReportController::class, 'weekChart'])
    ->name('report.week.chart');

/*
|--------------------------------------------------------------------------
| Month Reports
|--------------------------------------------------------------------------
*/
Route::get('/report/month/table', [ReportController::class, 'monthTable'])
    ->name('report.month.table');

Route::get('/report/month/chart', [ReportController::class, 'monthChart'])
    ->name('report.month.chart');

/*
|--------------------------------------------------------------------------
| All Reports
|--------------------------------------------------------------------------
*/
Route::get('/report/all/table', [ReportController::class, 'allTable'])
    ->name('report.all.table');

Route::get('/report/all/chart', [ReportController::class, 'allChart'])
    ->name('report.all.chart');
