<?php

use App\Models\Order;

use function Pest\Laravel\getJson;

test('GET /api/orders', function () {
    Order::factory(100)->create();
    getJson('/api/orders?symbol=BTC')
        ->assertOk()
        ->assertJsonCount(15, 'data');
});
