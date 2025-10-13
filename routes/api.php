<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AssetAssignmentController;
use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AssetRequestController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('users', [AuthController::class, 'users']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('dashboard', DashboardController::class);

    Route::apiResource('asset-categories', AssetCategoryController::class);
    Route::get('assets/code/{code}', [AssetController::class, 'showByCode']);
    Route::apiResource('assets', AssetController::class);
    Route::get('assets/{asset}/barcode', [AssetController::class, 'barcode']);
    Route::apiResource('asset-assignments', AssetAssignmentController::class)->only([
        'index', 'store', 'update', 'destroy', 'show',
    ]);
    Route::apiResource('asset-requests', AssetRequestController::class);
});
