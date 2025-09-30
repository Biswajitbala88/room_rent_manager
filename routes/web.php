<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DBSchemaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ----------------------
// Authentication Routes
// ----------------------
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

require __DIR__ . '/auth.php';


// ----------------------
// Dashboard Routes
// ----------------------
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/dashboard/monthly-summary', [DashboardController::class, 'monthlySummary']);
    Route::post('/dashboard/save-payments', [DashboardController::class, 'savePayments'])
        ->name('dashboard.save-payments');

    Route::get('/dashboard-summary', [DashboardController::class, 'getDashboardSummary']);
});


// ----------------------
// Profile Routes
// ----------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ----------------------
// Tenant Routes
// ----------------------
Route::middleware('auth')->group(function () {
    Route::resource('tenants', TenantController::class);
    Route::get('/tenants/{id}/due-invoices', [InvoiceController::class, 'getDueInvoices']);
});


// ----------------------
// Invoice Routes
// ----------------------
Route::middleware('auth')->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    // AJAX helpers
    Route::get('/tenant-last-units/{tenant_id}/{month}', [InvoiceController::class, 'getLastUnits']);
    Route::get('/invoices/last-units', [InvoiceController::class, 'getLastUnits_edit']);
    Route::post('/invoices/{id}/add-payment', [InvoiceController::class, 'addPayment']);
});


// ----------------------
// Utility Routes
// ----------------------
Route::get('/database-schema', [DBSchemaController::class, 'index']);
