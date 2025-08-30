<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\QuoteItemController;
use App\Http\Controllers\QuoteRequestController;

// Public Routes
Route::get('services', [ServiceController::class, 'index']);
Route::get('service-categories', [ServiceCategoryController::class, 'index']);
Route::post('quote-requests', [QuoteRequestController::class, 'store']);

// Authentication Routes (flat, no extra prefix)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me',      [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Protected API Routes
Route::middleware('auth:api')->group(function () {
    // Customers
    Route::apiResource('customers', CustomerController::class)->except(['show']);

    // Quotes
    Route::apiResource('quotes', QuoteController::class)->except(['show', 'destroy']);
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf']);
    Route::put('quotes/{quote}/status', [QuoteController::class, 'updateStatus']);

    // Services
    Route::apiResource('services', ServiceController::class)->except(['index', 'show']);
});

