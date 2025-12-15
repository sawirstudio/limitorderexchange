<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetSymbolPrice;
use App\Enums\OrderStatus;
use App\Enums\Symbol;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLimitOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateLimitOrderController extends Controller
{
    public function __invoke(CreateLimitOrderRequest $request, GetSymbolPrice $getSymbolPrice)
    {
        return new OrderResource(
            DB::transaction(function () use ($request, $getSymbolPrice) {
                $order = Order::create([
                    'side' => $request->boolean('side'),
                    'symbol' => $request->symbol,
                    'status' => OrderStatus::OPEN,
                    'price' => $getSymbolPrice($request->enum('symbol', Symbol::class)),
                    'amount' => $request->amount,
                    'user_id' => $request->user()->getKey(),
                ]);

                if ($order->side)  {
                    DB::statement('update users set balance = balance - (select (amount * price) from orders where orders.id = ? and orders.status = 1 and orders.user_id = users.id) where users.id = ? ', [$order->getKey(), $order->user_id]);
                } else {
                    DB::statement('with ord as (select amount from orders where orders.id = ? and orders.status = 1) update assets set amount = amount - (select ord.amount from ord), locked_amount = (select ord.amount from ord) where assets.user_id = ? and assets.symbol = ? ', [$order->getKey(), $order->user_id, $order->symbol->value]);
                }

                return $order;
            })
        );
    }
}
