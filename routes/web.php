<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormEntryController;
use App\Http\Controllers\FormEntryValueController;
use App\Http\Controllers\FormFieldController;
use Illuminate\Support\Facades\Route;

// ---------------------------
// FORMS
// ---------------------------

Route::middleware('auth')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('forms.index');


Route::post('/forms/store', [FormController::class, 'store'])->name('forms.store');
Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update');
Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
Route::get('/forms/ajax/list', [FormController::class, 'ajaxList'])->name('forms.ajax.list');

// ---------------------------
// FORM FIELDS
// ---------------------------
Route::get('/forms/{form}/fields', [FormFieldController::class, 'index'])->name('form-fields.index');
Route::get('/forms/{form}/fields/create', [FormFieldController::class, 'create'])->name('form-fields.create');
Route::post('/forms/{form}/fields/store', [FormFieldController::class, 'store'])->name('form-fields.store');
Route::get('/forms/{form}/fields/{field}/edit', [FormFieldController::class, 'edit'])->name('form-fields.edit');
Route::put('/forms/{form}/fields/{field}', [FormFieldController::class, 'update'])->name('form-fields.update');
Route::delete('/forms/{form}/fields/{field}', [FormFieldController::class, 'destroy'])->name('form-fields.destroy');
Route::get('/forms/{form}/fields/ajax', [FormFieldController::class, 'ajaxList'])->name('form-fields.ajax');

// ---------------------------
// USER FORM FILL & SUBMIT
// ---------------------------
Route::get('/forms/{form}/fill', [FormEntryValueController::class, 'fill'])->name('forms.fill');
Route::post('/forms/{form}/submit', [FormEntryValueController::class, 'submit'])->name('forms.submit');

// ---------------------------
// FORM ENTRIES (CRUD + DATATABLES AJAX)
// ---------------------------
Route::prefix('/forms/{form}/entries')->group(function () {

    Route::get('/', [FormEntryController::class, 'index'])->name('forms.entries');
    Route::get('/ajax', [FormEntryController::class, 'ajax'])->name('forms.entries.ajax');

});

Route::prefix('/entries')->group(function () {

    Route::get('/{entry}', [FormEntryController::class, 'show'])->name('entries.show');
    Route::get('/{entry}/edit', [FormEntryController::class, 'edit'])->name('entries.edit');
    Route::put('/{entry}', [FormEntryController::class, 'update'])->name('entries.update');
    Route::delete('/{entry}', [FormEntryController::class, 'destroy'])->name('entries.destroy');

});
Route::get('/forms/{form}/entries', [FormEntryController::class,'index'])->name('forms.entries');
Route::get('/forms/{form}/entries/ajax', [FormEntryController::class,'ajax'])->name('forms.entries.ajax');
Route::get('/entries/{entry}', [FormEntryController::class,'show']);
Route::get('/entries/{entry}/edit', [FormEntryController::class,'edit']);
Route::put('/entries/{entry}', [FormEntryController::class,'update']);
Route::delete('/entries/{entry}', [FormEntryController::class,'destroy']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/forms/ajax/search', [FormController::class, 'ajaxSearch'])->name('forms.ajax.search');


Route::middleware('auth')->get('/report', function () {
    return view('report.report');
})->name('report.report');

}); 

    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');





