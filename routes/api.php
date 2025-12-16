<?php

use App\Http\Controllers\Api\CancelLimitOrderController;
use App\Http\Controllers\Api\CreateLimitOrderController;
use App\Http\Controllers\Api\GetOrderbookController;
use App\Http\Controllers\Api\GetOrdersController;
use App\Http\Controllers\Api\GetProfileController;
use App\Http\Controllers\Api\PersonalAccessTokenController;
use Illuminate\Support\Facades\Route;

Route::get('profile', GetProfileController::class)->middleware(['auth:sanctum']);
Route::get('orders', GetOrdersController::class)->middleware(['auth:sanctum']);
Route::post('orders', CreateLimitOrderController::class)->middleware(['auth:sanctum']);
Route::post('orders/{order}/cancel', CancelLimitOrderController::class)->middleware(['auth:sanctum'])->can(
    'cancel',
    'order',
);
Route::post('personal-access-tokens', [PersonalAccessTokenController::class, 'store']);
Route::delete('personal-access-token', [PersonalAccessTokenController::class, 'destroy'])->middleware(['auth:sanctum']);
Route::get('orderbook', GetOrderbookController::class);
