<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
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
                ->where('status', OrderStatus::OPEN)
                ->where('symbol', strtoupper($request->query('symbol', 'BTC')))
                ->orderByDesc('created_at')
                ->cursorPaginate(),
        );
    }
}
