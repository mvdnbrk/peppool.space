<?php

namespace Tests\Feature\Api;

use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PricesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function prices_endpoint_returns_json(): void
    {
        $timestamp = 1757073600;
        $createdAt = Carbon::createFromTimestamp($timestamp);

        // Create deterministic records so cache/service picks a known timestamp
        Price::factory()->usd()->create([
            'price' => 0.0001,
            'created_at' => $createdAt,
        ]);
        Price::factory()->eur()->create([
            'price' => 0.00009,
            'created_at' => $createdAt,
        ]);

        $response = $this->get(route('api.prices'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonStructure([
                'USD',
                'EUR',
                'timestamp',
            ]);

        $data = $response->json();

        // USD & EUR should be numeric (float-like)
        $this->assertTrue(is_numeric($data['USD']), 'USD should be numeric');
        $this->assertTrue(is_numeric($data['EUR']), 'EUR should be numeric');

        // Assert exact price values (allowing for float precision)
        $this->assertEqualsWithDelta(0.0001, (float) $data['USD'], 1e-12);
        $this->assertEqualsWithDelta(0.00009, (float) $data['EUR'], 1e-12);

        // timestamp should exist and be an integer Unix timestamp
        $this->assertIsInt($data['timestamp']);
        $this->assertSame($timestamp, $data['timestamp']);

        // Also ensure the exact timestamp is present in JSON
        $response->assertJsonFragment(['timestamp' => $timestamp]);
    }
}
