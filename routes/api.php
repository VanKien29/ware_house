<?php

use App\Http\Controllers\Api\ModelKitManufacturerController;
use App\Http\Controllers\Api\ModelKitSeriesController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('product-variants', ProductVariantController::class);
    Route::apiResource('model-kit-manufacturers', ModelKitManufacturerController::class);
    Route::apiResource('model-kit-series', ModelKitSeriesController::class);
    Route::apiResource('warehouses', WarehouseController::class);
});
