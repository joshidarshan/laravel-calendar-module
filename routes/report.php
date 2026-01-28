<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/report/table', [ReportController::class, 'index'])
    ->name('report.table');

Route::get('/report/data', [ReportController::class, 'data'])
    ->name('report.data');

Route::get('/report/chart-data', [ReportController::class, 'chartData'])
    ->name('report.chart.data');
// Route::get('report', [ReportController::class, 'index'])->name('report.index');
// Route::get('report/data', [ReportController::class, 'data'])->name('report.data');
// Route::get('report/chart-data', [ReportController::class, 'chartData'])->name('report.chart.data');

// Route::post('employee-task', [EmployeeTaskController::class, 'store'])->name('employee-task.store');
// Route::put('employee-task/{task}', [EmployeeTaskController::class, 'update'])->name('employee-task.update');