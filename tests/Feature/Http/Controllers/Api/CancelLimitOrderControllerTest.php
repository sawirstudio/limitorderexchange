<?php

use App\Models\Order;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('cancel order', function () {
    actingAs($user = user());
    $order = Order::factory()->for($user)->create();
    postJson("/api/orders/{$order->getKey()}/cancel")
        ->assertNoContent();
});
