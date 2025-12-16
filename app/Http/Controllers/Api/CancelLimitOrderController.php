<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelLimitOrderController extends Controller
{
    public function __invoke(Order $order, Request $request)
    {
        match ($order->status) {
            OrderStatus::CANCELED => throw ValidationException::withMessages([
                'status' => ['Order is already canceled'],
            ]),
            OrderStatus::FILLED => throw ValidationException::withMessages([
                'status' => ['Order is already filled'],
            ]),
            default => null,
        };

        DB::select("with ord as (
        update orders set status = 3 where id = ? returning user_id, symbol, side, (price * amount) as volume
        ),
        release_locked_asset as (
            update assets set amount = amount + locked_amount, locked_amount = 0 where exists(select 1 from ord where ord.side is false and ord.user_id = assets.user_id and ord.symbol = assets.symbol) returning amount
        ),
        release_locked_balance as (
            update users set balance = balance + (select volume from ord) where exists(select 1 from ord where ord.side is true and ord.user_id = users.id) returning balance
        )
        select 1", [$order->getKey()]);

        return response()->noContent();
    }
}
