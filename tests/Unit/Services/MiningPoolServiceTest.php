<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Pool;
use App\Services\MiningPoolService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MiningPoolServiceTest extends TestCase
{
    use RefreshDatabase;

    private MiningPoolService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MiningPoolService;
        Cache::forget('mining_pools_list');
    }

    public function test_it_identifies_pool_by_tag(): void
    {
        $pool = Pool::create([
            'name' => 'ViaBTC',
            'slug' => 'viabtc',
            'addresses' => [],
            'regexes' => ['ViaBTC'],
        ]);

        // Mock coinbase script containing the tag
        $scriptHex = bin2hex('...ViaBTC...');

        $identified = $this->service->identifyPool($scriptHex, 'some-address');

        $this->assertNotNull($identified);
        $this->assertEquals($pool->id, $identified->id);
    }

    public function test_it_identifies_pool_by_address_fallback(): void
    {
        $pool = Pool::create([
            'name' => 'F2Pool',
            'slug' => 'f2pool',
            'addresses' => ['Pf2PoolAddress'],
            'regexes' => ['F2Pool'],
        ]);

        // No tag in script, but address matches
        $identified = $this->service->identifyPool(bin2hex('no-tag'), 'Pf2PoolAddress');

        $this->assertNotNull($identified);
        $this->assertEquals($pool->id, $identified->id);
    }

    public function test_it_prioritizes_tag_over_address(): void
    {
        $poolA = Pool::create([
            'name' => 'Pool A',
            'slug' => 'pool-a',
            'addresses' => ['AddressX'],
            'regexes' => ['TagA'],
        ]);

        $poolB = Pool::create([
            'name' => 'Pool B',
            'slug' => 'pool-b',
            'addresses' => ['AddressX'], // Same address as Pool A
            'regexes' => ['TagB'],
        ]);

        // Script has TagB, but Address is AddressX (which Pool A also has)
        $identified = $this->service->identifyPool(bin2hex('...TagB...'), 'AddressX');

        $this->assertNotNull($identified);
        $this->assertEquals($poolB->id, $identified->id, 'Should prioritize tag match');
    }

    public function test_it_records_new_payout_addresses(): void
    {
        $pool = Pool::create([
            'name' => 'EMCD',
            'slug' => 'emcd',
            'addresses' => ['OldAddress'],
            'regexes' => ['emcd'],
        ]);

        $newAddress = 'NewAddress123';

        $this->service->recordPayoutAddress($pool, $newAddress);

        $pool->refresh();
        $this->assertContains($newAddress, $pool->addresses);
        $this->assertContains('OldAddress', $pool->addresses);
    }

    public function test_it_limits_recorded_addresses_to_five(): void
    {
        $pool = Pool::create([
            'name' => 'ViaBTC',
            'slug' => 'viabtc',
            'addresses' => ['addr1', 'addr2', 'addr3', 'addr4', 'addr5'],
            'regexes' => ['viabtc'],
        ]);

        $this->service->recordPayoutAddress($pool, 'addr6');

        $pool->refresh();
        $this->assertCount(5, $pool->addresses);
        $this->assertContains('addr6', $pool->addresses);
        $this->assertNotContains('addr1', $pool->addresses);
    }
}
