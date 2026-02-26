<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\CalculateMiningStats;
use App\Models\Block;
use App\Models\Pool;
use App\Models\PoolStat;
use App\Services\PepecoinExplorerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class CalculateMiningStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_daily_and_weekly_stats(): void
    {
        $pool = Pool::create([
            'name' => 'ViaBTC',
            'slug' => 'viabtc',
            'addresses' => [],
            'regexes' => [],
        ]);

        // Create 10 blocks in the last hour
        // We set a fixed time to avoid intermittent issues with "now" shifting during the test
        $now = Carbon::parse('2026-02-26 12:30:00');
        Carbon::setTestNow($now);

        Block::factory()->count(10)->create([
            'pool_id' => $pool->id,
            'created_at' => $now->copy()->subMinutes(30),
            'difficulty' => 1000,
        ]);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        
        // Dispatch job
        CalculateMiningStats::dispatchSync($explorer);

        // Check daily stats
        // Note: hashrate_timestamp is $end->startOfHour() which is $now->startOfHour()
        $timestamp = $now->copy()->startOfHour();

        $daily = PoolStat::where('type', 'daily')
            ->where('pool_id', $pool->id)
            ->where('hashrate_timestamp', $timestamp)
            ->first();
        $this->assertNotNull($daily);
        $this->assertEquals(1.0, $daily->share);
        
        // Formula: (1000 * 2^32 * 10) / 86400 (window is 24h)
        $expectedHashrate = (1000 * 4294967296.0 * 10) / 86400;
        $this->assertEqualsWithDelta($expectedHashrate, $daily->avg_hashrate, 0.0001);

        // Check weekly stats
        $weekly = PoolStat::where('type', 'weekly')
            ->where('pool_id', $pool->id)
            ->where('hashrate_timestamp', $timestamp)
            ->first();
        $this->assertNotNull($weekly);
        
        // Formula: (1000 * 2^32 * 10) / 604800 (window is 7d)
        $expectedWeeklyHashrate = (1000 * 4294967296.0 * 10) / 604800;
        $this->assertEqualsWithDelta($expectedWeeklyHashrate, $weekly->avg_hashrate, 0.0001);

        Carbon::setTestNow(); // Reset time
    }

    public function test_it_handles_multiple_pools_and_unknown(): void
    {
        $poolA = Pool::create(['name' => 'Pool A', 'slug' => 'a', 'addresses' => [], 'regexes' => []]);
        $unknownPool = Pool::create(['name' => 'Unknown', 'slug' => 'unknown', 'addresses' => [], 'regexes' => []]);

        $now = Carbon::parse('2026-02-26 12:30:00');
        Carbon::setTestNow($now);
        
        // 8 blocks for Pool A
        Block::factory()->count(8)->create([
            'pool_id' => $poolA->id,
            'created_at' => $now->copy()->subMinutes(10),
        ]);

        // 2 blocks for Unknown (null pool_id)
        Block::factory()->count(2)->create([
            'pool_id' => null,
            'created_at' => $now->copy()->subMinutes(10),
        ]);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        
        CalculateMiningStats::dispatchSync($explorer);

        $this->assertEquals(0.8, PoolStat::where('pool_id', $poolA->id)->value('share'));
        $this->assertEquals(0.2, PoolStat::where('pool_id', $unknownPool->id)->value('share'));

        Carbon::setTestNow(); // Reset time
    }
}
