<?php

namespace Tests\Feature\Api;

use App\Services\PepecoinExplorerService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlocksTipHeightTest extends TestCase
{
    #[Test]
    public function tip_height_returns_json_number(): void
    {
        $mock = \Mockery::mock(PepecoinExplorerService::class);
        $mock->shouldReceive('getBlockTipHeight')->once()->andReturn(655981);
        $this->app->instance(PepecoinExplorerService::class, $mock);

        $this->get(route('api.blocks.tip.height'))
            ->assertStatus(200)
            ->assertJson([
                'height' => 655981,
            ]);
    }
}
