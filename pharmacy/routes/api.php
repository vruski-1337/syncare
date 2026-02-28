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
    // subscriptions managed by admin only via web, can expose read-only if needed
});
