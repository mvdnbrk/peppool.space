<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiningApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_mining_pool_shares(): void
    {
        $pool = Pool::create(['name' => 'ViaBTC', 'slug' => 'viabtc', 'addresses' => [], 'regexes' => []]);
        
        $now = now()->startOfHour();
        PoolStat::create([
            'hashrate_timestamp' => $now,
            'pool_id' => $pool->id,
            'avg_hashrate' => 1000000,
            'share' => 0.5,
            'type' => 'daily',
        ]);

        $this->get(route('api.mining.pools', ['type' => 'daily']))
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'ViaBTC',
                'share' => 0.5,
            ]);
    }

    public function test_it_returns_hashrate_history(): void
    {
        $pool = Pool::create(['name' => 'ViaBTC', 'slug' => 'viabtc', 'addresses' => [], 'regexes' => []]);
        $now = now()->startOfHour();

        PoolStat::create([
            'hashrate_timestamp' => $now,
            'pool_id' => $pool->id,
            'avg_hashrate' => 1000000,
            'share' => 1.0,
            'type' => 'daily',
        ]);

        $this->get(route('api.mining.hashrate'))
            ->assertOk()
            ->assertJsonStructure([
                '*' => ['timestamp', 'pools', 'totalHashrate']
            ]);
    }

    public function test_it_returns_mining_blocks(): void
    {
        $pool = Pool::create(['name' => 'ViaBTC', 'slug' => 'viabtc', 'addresses' => [], 'regexes' => []]);
        Block::factory()->create([
            'height' => 100,
            'pool_id' => $pool->id,
        ]);

        $this->get(route('api.mining.blocks'))
            ->assertOk()
            ->assertJsonFragment([
                'height' => 100,
                'pool' => [
                    'name' => 'ViaBTC',
                    'slug' => 'viabtc',
                ]
            ]);
    }
}
