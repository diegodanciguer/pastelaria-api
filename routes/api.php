<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

// Client Routes
Route::prefix('clients')->group(function () {
    Route::get('/list', [ClientController::class, 'list']);
    Route::post('/create', [ClientController::class, 'create']);
    Route::get('/detail/{id}', [ClientController::class, 'show']);
    Route::put('/detail/{id}', [ClientController::class, 'update']);
    Route::delete('/delete/{id}', [ClientController::class, 'delete']);
    Route::post('/restore/{id}', [ClientController::class, 'restore']);
});

// Rotas para Produtos
Route::prefix('products')->group(function () {
    Route::get('/list', [ProductController::class, 'list']);
    Route::post('/create', [ProductController::class, 'create']);
    Route::get('/detail/{id}', [ProductController::class, 'show']);
    Route::put('/detail/{id}', [ProductController::class, 'update']);
    Route::delete('/delete/{id}', [ProductController::class, 'delete']);
    Route::post('/restore/{id}', [ProductController::class, 'restore']);
});

// Order Routes
Route::prefix('orders')->group(function () {
    Route::get('/list', [OrderController::class, 'list']);
    Route::post('/create', [OrderController::class, 'create']);
    Route::get('/detail/{id}', [OrderController::class, 'show']);
    Route::put('/detail/{id}', [OrderController::class, 'update']);
    Route::delete('/delete/{id}', [OrderController::class, 'delete']);
    Route::post('/restore/{id}', [OrderController::class, 'restore']);
});
