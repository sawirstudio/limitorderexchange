<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetSymbolPrice;
use App\Enums\OrderStatus;
use App\Enums\Symbol;
use App\Events\OrderMatched;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLimitOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateLimitOrderController extends Controller
{
    public function __invoke(CreateLimitOrderRequest $request)
    {
        return new OrderResource(DB::transaction(function () use ($request) {
            $order = Order::create([
                'side' => $request->boolean('side'),
                'symbol' => $request->symbol,
                'status' => OrderStatus::OPEN,
                'price' => $request->price,
                'amount' => $request->amount,
                'user_id' => $request->user()->getKey(),
            ]);

            if ($order->side) {
                DB::statement(
                    'update users set balance = balance - (select (amount * price) from orders where orders.id = ? and orders.status = 1 and orders.user_id = users.id) where users.id = ? ',
                    [$order->getKey(), $order->user_id],
                );
            } else {
                DB::statement(
                    'with ord as (select amount from orders where orders.id = ? and orders.status = 1) update assets set amount = amount - (select ord.amount from ord), locked_amount = (select ord.amount from ord) where assets.user_id = ? and assets.symbol = ? ',
                    [$order->getKey(), $order->user_id, $order->symbol->value],
                );
            }

            // attempt matching
            $makerorder = Order::query()
                ->whereNot('user_id', $order->user_id)
                ->where('side', !$order->side)
                ->where('status', OrderStatus::OPEN)
                ->where('symbol', $order->symbol)
                ->where('price', $order->side ? '<=' : '>=', $order->price)
                ->orderBy('price')
                ->lockForUpdate()
                ->first();

            if ($makerorder) {
                $trade = Trade::create([
                    'fee' => 1.5,
                    'price' => $makerorder->price,
                    'amount' => min($makerorder->amount, $order->amount),
                    'makerorder_id' => $makerorder->getKey(),
                    'order_id' => $order->getKey(),
                ]);
                DB::statement(
                    'with
                        ord as (update orders set status = 2, trade_id = ? where id = ? returning user_id, trade_id, amount, symbol),
                        trd as (select amount, price, (amount * price) as volume, fee / 100 as fee from trades where id = (select trade_id from ord)),
                        release_asset as (update assets set amount = amount + (select ord.amount from ord) - (select trd.amount from trd), locked_amount = 0 where locked_amount > 0 and exists(select 1 from ord where ord.user_id = assets.user_id and ord.symbol = assets.symbol) returning amount, locked_amount)
                        update users set balance = balance + (select volume - (volume * fee) from trd) where users.id = (select user_id from ord)',
                    [$trade->getKey(), $order->side ? $makerorder->getKey() : $order->getKey()],
                );
                DB::statement('with
                        ord as (update orders set status = 2, trade_id = ? where id = ? returning trade_id, user_id, amount, symbol, (price * amount) as volume),
                        trd as (select amount, price, (amount * price) as volume, fee / 100 as fee from trades where id = (select trade_id from ord)),
                        add_asset as (update assets set amount = amount + (select trd.amount from trd) where assets.user_id = (select ord.user_id from ord) and assets.symbol = (select ord.symbol from ord))
                        update users set balance = balance + (select ord.volume from ord) - (select trd.volume from trd) where users.id = (select user_id from ord)
                    ', [
                    $trade->getKey(),
                    $order->side ? $order->getKey() : $makerorder->getKey(),
                ]);
                event(new OrderMatched($order));
                event(new OrderMatched($makerorder));
            }

            return $order;
        }));
    }
}
