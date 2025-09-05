<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'tx_id' => $this->faker->sha256(),
            'block_height' => $this->faker->numberBetween(1, 999999),
            'size' => $this->faker->numberBetween(200, 2000),
            'fee' => $this->faker->randomFloat(8, 0, 0.001),
            'version' => $this->faker->numberBetween(1, 2),
            'locktime' => $this->faker->numberBetween(0, 999999),
            'is_coinbase' => false,
        ];
    }

    public function coinbase(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_coinbase' => true,
            'fee' => 0,
        ]);
    }
}
