<?php

use App\Models\Order;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

test('GET /api/orders', function () {
    actingAs($user = user());
    Order::factory(100)->for($user)->create();
    getJson('/api/orders?symbol=BTC')
        ->assertOk()
        ->assertJsonCount(15, 'data');
});
