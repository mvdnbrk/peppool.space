<?php

namespace Database\Factories;

use App\Models\WalletUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletUser>
 */
class WalletUserFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => 'P'.fake()->regexify('[A-Za-z0-9]{33}'),
            'version' => '0.1.0',
        ];
    }
}
