<?php

namespace Database\Factories;

use App\Models\AddressBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AddressBalance>
 */
class AddressBalanceFactory extends Factory
{
    protected $model = AddressBalance::class;

    public function definition(): array
    {
        return [
            // Pepecoin address format: starts with 'P' followed by Base58 chars (exclude 0,O,I,l)
            'address' => $this->faker->regexify('P[1-9A-HJ-NP-Za-km-z]{25,33}'),
            'balance' => $this->faker->randomFloat(8, 0, 1_000_000),
            'total_received' => $this->faker->randomFloat(8, 0, 2_000_000),
            'total_sent' => $this->faker->randomFloat(8, 0, 2_000_000),
            'tx_count' => $this->faker->numberBetween(0, 5000),
            'first_seen' => $this->faker->dateTimeBetween('-5 years', '-1 years'),
            'last_activity' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
