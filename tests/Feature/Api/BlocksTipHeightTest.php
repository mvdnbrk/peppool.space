<?php

namespace Tests\Feature\Api;

use App\Contracts\BlockchainServiceInterface;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlocksTipHeightTest extends TestCase
{
    #[Test]
    public function tip_height_returns_plain_text_height(): void
    {
        $mock = Mockery::mock(BlockchainServiceInterface::class);
        $mock->shouldReceive('getBlockTipHeight')->once()
            ->andReturn(123456);
        $this->app->instance(BlockchainServiceInterface::class, $mock);

        $this->get(route('api.blocks.tip.height'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('123456');
    }
}
