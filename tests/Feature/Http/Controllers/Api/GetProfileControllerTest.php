<?php

use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

test('GET /api/profile', function () {
    actingAs($user = user());
    getJson('/api/profile')
        ->assertOk()
        ->assertJson(function (AssertableJson $json) use ($user) {
            return $json->where('data.name', $user->name)
                ->where('data.balance', $user->balance)
                ->where('data.assets_sum_amount', $user->assets()->whereNull('locked_amount')->sum('amount'));
        });
});