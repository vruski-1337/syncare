<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome');
    }

    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (! $user->company_id) {
        return view('dashboard');
    }

    return redirect()->route('company.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

// admin panel routes
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
    Route::resource('companies', CompanyController::class);
    Route::resource('subscriptions', App\Http\Controllers\SubscriptionController::class);
    Route::post('reset-credentials', [AdminController::class,'resetCredentials'])->name('reset-credentials');
    Route::get('settings', [AdminController::class,'settings'])->name('settings');
    Route::post('settings', [AdminController::class,'saveSettings']);
    // additional admin-specific actions can be added here
});

// company routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/company/dashboard', [CompanyController::class,'dashboard'])->name('company.dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
});
