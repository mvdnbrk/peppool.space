<?php

namespace Database\Factories;

use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'currency' => $this->faker->randomElement(['USD', 'EUR']),
            'price' => $this->faker->randomFloat(8, 0.00001, 0.001),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function usd(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => 'USD',
        ]);
    }

    public function eur(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => 'EUR',
        ]);
    }
}
