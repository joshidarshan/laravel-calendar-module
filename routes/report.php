<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/report/table', [ReportController::class, 'table'])->name('report.table');
Route::get('/report/charts', [ReportController::class, 'charts'])->name('report.charts');
