<?php

namespace Tests\Feature\Api;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ThrottlingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_routes_have_throttle_middleware(): void
    {
        $apiRoutes = [
            'api.prices',
            'api.blocks.tip.height',
            'api.blocks.tip.hash',
            'api.blocks.list',
            'api.validate.address',
            'api.mempool.index',
            'api.mempool.txids',
            'api.chart.prices',
        ];

        foreach ($apiRoutes as $routeName) {
            $route = app('router')->getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Route {$routeName} should exist");
            $this->assertContains('throttle:api', $route->middleware(),
                "Route {$routeName} should have throttle middleware");
        }
    }

    #[Test]
    public function throttled_api_requests_return_json(): void
    {
        RateLimiter::for('api', fn () => Limit::perMinute(1));

        $this->get(route('api.prices'));
        $response = $this->get(route('api.prices'));

        $response->assertStatus(429);
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonStructure(['message']);
    }
}
