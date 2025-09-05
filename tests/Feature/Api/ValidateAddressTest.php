<?php

namespace Tests\Feature\Api;

use App\Services\PepecoinExplorerService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidateAddressTest extends TestCase
{
    #[Test]
    public function test_validate_address_returns_expected_shape(): void
    {
        $mock = Mockery::mock(PepecoinExplorerService::class);
        $mock->shouldReceive('validateAddress')->once()->with('PValidAddress123')
            ->andReturn(collect([
                'isvalid' => true,
                'address' => 'PValidAddress123',
                'scriptPubKey' => '76a914...88ac',
                'isscript' => false,
            ]));
        $this->app->instance(PepecoinExplorerService::class, $mock);

        $this->get(route('api.validate.address', ['address' => 'PValidAddress123']))
            ->assertOk()
            ->assertJsonStructure([
                'isvalid', 'address', 'scriptPubKey', 'isscript',
            ])
            ->assertJson([
                'isvalid' => true,
                'address' => 'PValidAddress123',
                'isscript' => false,
            ]);
    }
}
