<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

// admin panel routes
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class,'dashboard'])->name('dashboard');
    Route::resource('companies', App\Http\Controllers\CompanyController::class);
    Route::resource('subscriptions', App\Http\Controllers\SubscriptionController::class);
    Route::post('reset-credentials', [App\Http\Controllers\AdminController::class,'resetCredentials'])->name('reset-credentials');
    Route::get('settings', [App\Http\Controllers\AdminController::class,'settings'])->name('settings');
    Route::post('settings', [App\Http\Controllers\AdminController::class,'saveSettings']);
    // additional admin-specific actions can be added here
});

// company routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/company/dashboard', [App\Http\Controllers\CompanyController::class,'dashboard'])->name('company.dashboard');
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
});
