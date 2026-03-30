<?php

namespace Database\Factories;

use App\Models\Node;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Node>
 */
class NodeFactory extends Factory
{
    protected $model = Node::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip' => $this->faker->unique()->ipv4(),
            'port' => 33874,
            'version' => 70016,
            'subversion' => '/pepetoshi:1.1.0/',
            'continent' => 'Europe',
            'continent_code' => 'EU',
            'country' => 'Netherlands',
            'country_code' => 'NL',
            'region' => 'North Holland',
            'region_code' => 'NH',
            'city' => 'Haarlem',
            'latitude' => 52.3695,
            'longitude' => 4.6359,
            'isp' => 'Concepts ICT BV',
            'is_online' => true,
            'last_seen_at' => now(),
            'sources' => ['local'],
        ];
    }
}
