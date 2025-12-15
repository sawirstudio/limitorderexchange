<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Validation\ValidationException;

class CancelLimitOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Order $order)
    {
        match ($order->status) {
            OrderStatus::CANCELED => throw ValidationException::withMessages([
                'status' => ['Order is already canceled'],
            ]),
            OrderStatus::FILLED => throw ValidationException::withMessages([
                'status' => ['Order is already filled'],
            ]),
            OrderStatus::OPEN => $order->update([
                'status' => OrderStatus::CANCELED,
            ]),
        };

        return response()->noContent();
    }
}
