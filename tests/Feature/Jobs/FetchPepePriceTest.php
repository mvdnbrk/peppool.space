<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\FetchPepePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchPepePriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_and_stores_prices(): void
    {
        Http::fake([
            '*simple/price*' => Http::response([
                'pepecoin-network' => [
                    'usd' => 0.00012345,
                    'eur' => 0.00011234,
                ],
            ]),
        ]);

        (new FetchPepePrice)->handle();

        $this->assertDatabaseHas('prices', [
            'currency' => 'USD',
            'price' => 0.00012345,
        ]);
        $this->assertDatabaseHas('prices', [
            'currency' => 'EUR',
            'price' => 0.00011234,
        ]);
        $this->assertEquals(0.00012345, Cache::get('pepecoin_price_usd'));
        $this->assertEquals(0.00011234, Cache::get('pepecoin_price_eur'));
    }

    public function test_it_does_not_store_on_failed_response(): void
    {
        Http::fake([
            '*simple/price*' => Http::response(null, 500),
        ]);

        (new FetchPepePrice)->handle();

        $this->assertDatabaseCount('prices', 0);
        $this->assertNull(Cache::get('pepecoin_price_usd'));
    }

    public function test_it_updates_existing_price_for_same_timestamp(): void
    {
        Http::fake([
            '*simple/price*' => Http::sequence()
                ->push(['pepecoin-network' => ['usd' => 0.00010000, 'eur' => 0.00009000]])
                ->push(['pepecoin-network' => ['usd' => 0.00020000, 'eur' => 0.00018000]]),
        ]);

        (new FetchPepePrice)->handle();
        (new FetchPepePrice)->handle();

        $this->assertDatabaseCount('prices', 2);
        $this->assertDatabaseHas('prices', [
            'currency' => 'USD',
            'price' => 0.00020000,
        ]);
    }
}
