<?php

use App\Enums\Symbol;
use App\Models\Asset;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('can order BUY', function () {
    actingAs($user = user());
    $user->update(['balance' => 100000]);
    postJson('/api/orders', [
        'side' => true,
        'symbol' => 'BTC',
        'amount' => 1,
    ])->assertCreated();
    $user->refresh();
    expect($user->balance)->toEqual(0);
    expect($user->orders->first()->side)->toBe(true);
    expect($user->orders->first()->symbol)->toBe(Symbol::BTC);
    expect($user->orders->first()->amount)->toEqual(1);
});

test('can order SELL', function () {
    actingAs($user = user());
    $asset = Asset::factory()->for($user)->create([
        'amount' => 1,
    ]);
    postJson('/api/orders', [
        'side' => false,
        'symbol' => $asset->symbol->value,
        'amount' => $asset->amount,
    ])->assertCreated();
    $asset->refresh();
    expect($asset->amount)->toEqual(0);
    expect($asset->locked_amount)->toEqual($user->orders->first()->amount);
    expect($user->orders->first()->side)->toBe(false);
    expect($user->orders->first()->symbol)->toBe($asset->symbol);
});
