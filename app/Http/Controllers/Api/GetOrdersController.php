<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\Symbol;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class GetOrdersController extends Controller
{
    public function __invoke(Request $request)
    {
        return OrderResource::collection(
            resource: Order::query()
                ->when($request->filled('status'), fn($query) => $query->where(
                    'status',
                    $request->enum('status', OrderStatus::class)?->value ?? OrderStatus::OPEN->value,
                ))
                ->when($request->filled('symbol'), fn($query) => $query->where(
                    'symbol',
                    strtoupper($request->enum('symbol', Symbol::class)?->value ?? 'BTC'),
                ))
                ->when($request->filled('side'), fn($query) => $query->where('side', $request->boolean('side', true)))
                ->whereBelongsTo($request->user())
                ->orderByDesc('created_at')
                ->cursorPaginate(),
        );
    }
}
