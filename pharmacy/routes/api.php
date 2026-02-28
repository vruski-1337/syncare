<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// basic API routes for future integration
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/company', function (Request $request) {
        return $request->user()->company;
    });
    Route::apiResource('products', App\Http\Controllers\ProductController::class);
    Route::apiResource('invoices', App\Http\Controllers\InvoiceController::class);
    // HMS endpoints for future integration
    Route::get('/hms/company/{id}', [App\Http\Controllers\CompanyController::class, 'show']);
    // Add more HMS endpoints as needed
});
