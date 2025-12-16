<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'side' => fake()->boolean(),
            'symbol' => fake()->randomElement(['BTC', 'ETH']),
            'price' => fake()->numberBetween(0, 100000),
            'amount' => fake()->randomFloat(8, 0, 10),
            'user_id' => fake()->numberBetween(0,100),
        ];
    }
}
