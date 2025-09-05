<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition(): array
    {
        return [
            'height' => $this->faker->unique()->numberBetween(1, 999999),
            'hash' => $this->faker->sha256(),
            'tx_count' => $this->faker->numberBetween(1, 100),
            'size' => $this->faker->numberBetween(1000, 50000),
            'difficulty' => $this->faker->randomFloat(8, 0.1, 1000000),
            'nonce' => $this->faker->numberBetween(0, 4294967295),
            'version' => $this->faker->numberBetween(1, 4),
            'merkleroot' => $this->faker->sha256(),
            'chainwork' => $this->faker->hexColor(),
            'auxpow' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
