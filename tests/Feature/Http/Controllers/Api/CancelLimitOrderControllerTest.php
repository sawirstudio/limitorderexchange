<?php

use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\Order;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('cancel sell order', function () {
    actingAs($user = user());
    $order = Order::factory()->for($user)->create(['status' => OrderStatus::OPEN, 'side' => false]);
    $asset = Asset::factory()->for($user)->create(['locked_amount' => $order->amount, 'symbol' => $order->symbol]);
    $initialAmount = $asset->amount;
    postJson("/api/orders/{$order->getKey()}/cancel")
        ->assertNoContent();
    $order->refresh();
    $asset->refresh();
    expect($order->status)->toBe(OrderStatus::CANCELED);
    expect($asset->locked_amount)->toEqual(0);
    expect($asset->amount)->toEqual($initialAmount + $order->amount);
});

test('cancel buy order', function () {
    actingAs($user = user());
    $initialBalance = $user->balance;
    $order = Order::factory()->for($user)->create(['status' => OrderStatus::OPEN, 'side' => true]);
    postJson("/api/orders/{$order->getKey()}/cancel")
        ->assertNoContent();
    $order->refresh();
    $user->refresh();
    expect($order->status)->toBe(OrderStatus::CANCELED);
    expect($user->balance)->toEqual($initialBalance + ($order->price * $order->amount));
});
