<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Inscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscription>
 */
class InscriptionFactory extends Factory
{
    public function definition(): array
    {
        $txid = $this->faker->sha256();

        return [
            'id' => $this->faker->unique()->numberBetween(0, 999999),
            'inscription_id' => $txid.'i0',
            'parent_id' => null,
            'delegate_id' => null,
            'content_encoding' => null,
            'content_type' => $this->faker->randomElement(['text/plain', 'image/png', 'image/webp', 'application/json']),
            'content_length' => $this->faker->numberBetween(10, 100000),
            'content' => null,
            'properties' => null,
            'flags' => 0,
            'block' => Block::factory(),
        ];
    }
}
