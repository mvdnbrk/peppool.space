<?php

namespace Tests\Feature\Api;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ThrottlingTest extends TestCase
{
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
            $this->assertContains('throttle:60,1', $route->middleware(),
                "Route {$routeName} should have throttle middleware");
        }
    }
}
