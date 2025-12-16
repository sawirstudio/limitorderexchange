<?php

namespace App\Http\Controllers\Api;

use App\Enums\Symbol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetOrderbookController extends Controller
{
    public function __invoke(Request $request)
    {
        $results = DB::select('with ranked_buys as (
            select price buy_price, amount buy_amount,
            row_number() over (order by price desc) as rn,
            sum(amount) over (order by price desc) as buy_cumulative_amount
            from orders
            where symbol = ?
            and status = 1
            and side is true
        ),
        ranked_sells as (
            select price sell_price, amount sell_amount,
            row_number() over (order by price asc) as rn,
            sum(amount) over (order by price asc) as sell_cumulative_amount
            from orders
            where symbol = ?
            and status = 1
            and side is false
        )
        select coalesce(ranked_buys.rn, ranked_sells.rn) AS level,
        ranked_buys.buy_price, ranked_buys.buy_amount, ranked_buys.buy_cumulative_amount,
        ranked_sells.sell_price, ranked_sells.sell_amount, ranked_sells.sell_cumulative_amount
        from ranked_buys
        full outer join ranked_sells on ranked_buys.rn = ranked_sells.rn
        order by level', [
            $request->enum('symbol', Symbol::class)->value,
            $request->enum('symbol', Symbol::class)->value,
        ]);

        return response()->json(['data' => $results]);
    }
}
