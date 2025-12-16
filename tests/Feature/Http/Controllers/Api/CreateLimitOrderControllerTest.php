<?php

use App\Enums\Symbol;
use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

test('can order BUY', function () {
    actingAs($user = user());
    $user->update(['balance' => 100000]);
    postJson('/api/orders', [
        'side' => true,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 100000,
    ])->assertCreated();
    $user->refresh();
    expect($user->balance)->toEqual(0);
    expect($user->orders->first()->side)->toBe(true);
    expect($user->orders->first()->symbol)->toBe(Symbol::BTC);
    expect($user->orders->first()->amount)->toEqual(1);
});

test('can order SELL', function () {
    actingAs($user = user());
    $user->assets()->where('symbol','BTC')->update([
        'amount' => 1,
    ]);
    postJson('/api/orders', [
        'side' => false,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 1000,
    ])->assertCreated();

    $asset = Asset::where('symbol','BTC')->first();
    expect($asset->amount)->toEqual(0);
    expect($asset->locked_amount)->toEqual($user->orders->first()->amount);
    expect($user->orders->first()->side)->toBe(false);
    expect($user->orders->first()->symbol)->toBe($asset->symbol);
});

test('can order MATCHED. case BUY as makerorder and SELL as takerorder', function () {
    Event::fake();

    actingAs($user = user());
    $user->update(['balance' => 100000]);
    postJson('/api/orders', [
        'side' => true,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 100000,
    ])->assertCreated();

    actingAs($user2 = user());
    $user2->update(['balance' => 0]);
    $user2->assets()->where('symbol','BTC')->update([
        'amount' => 1,
    ]);
    postJson('/api/orders', [
        'side' => false,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 1000,
    ])->assertCreated();
    Event::assertDispatched(OrderMatched::class);
    assertDatabaseHas(Trade::class, [
        'makerorder_id' => $user->orders->first()->getKey(),
        'order_id' => $user2->orders->first()->getKey(),
        'amount' => 1,
        'price' => 100000,
    ]);
    $user->refresh();
    $user2->refresh();
    expect($user->balance)->toEqual(0);
    expect($user->assets->first()->amount)->toEqual(1);
    expect($user2->assets->first()->amount)->toEqual(0);
    expect($user2->balance)->toEqual(100000 - (100000 * 1.5 / 100)); // minus fee 1.5%
});

test('can order MATCHED case SELL as makerorder and BUY as takerorder', function () {
    Event::fake();

    actingAs($user = user());
    $user->update(['balance' => 0]);
    $user->assets()->where('symbol','BTC')->update([
        'amount' => 1,
    ]);
    postJson('/api/orders', [
        'side' => false,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 100000,
    ])->assertCreated();

    actingAs($user2 = user());
    $user2->update(['balance' => 100000]);
    postJson('/api/orders', [
        'side' => true,
        'symbol' => 'BTC',
        'amount' => 1,
        'price' => 100000,
    ])->assertCreated();
    Event::assertDispatched(OrderMatched::class);
    assertDatabaseHas(Trade::class, [
        'makerorder_id' => $user->orders->first()->getKey(),
        'order_id' => $user2->orders->first()->getKey(),
        'amount' => 1,
        'price' => 100000,
    ]);
    $user->refresh();
    $user2->refresh();
    expect($user2->balance)->toEqual(0);
    expect($user2->assets->first()->amount)->toEqual(1);
    expect($user->assets->first()->amount)->toEqual(0);
    expect($user->balance)->toEqual(100000 - (100000 * 1.5 / 100)); // minus fee 1.5%
});
