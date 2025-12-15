<?php

use App\Http\Controllers\Api\CancelLimitOrderController;
use App\Http\Controllers\Api\CreateLimitOrderController;
use App\Http\Controllers\Api\GetOrdersController;
use App\Http\Controllers\Api\GetProfileController;
use Illuminate\Support\Facades\Route;

Route::get('profile', GetProfileController::class)
    ->middleware(['auth:sanctum']);
Route::get('orders', GetOrdersController::class);
Route::post('orders/{order}/cancel', CancelLimitOrderController::class)
    ->middleware(['auth:sanctum'])
    ->can('cancel', 'order');
