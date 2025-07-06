<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/dashboard/save-payments', [DashboardController::class, 'savePayments'])->name('dashboard.save-payments');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});
Route::resource('tenants', TenantController::class)->middleware(['auth']);
Route::resource('invoices', InvoiceController::class)->middleware(['auth']);
Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
// Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');

// AJAX: Get last month's electricity units for a tenant
Route::get('/tenant-last-units/{tenant_id}/{month}', [InvoiceController::class, 'getLastUnits']);
Route::get('/invoices/last-units', [InvoiceController::class, 'getLastUnits_edit']);





require __DIR__.'/auth.php';
