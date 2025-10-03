<?php

namespace Tests\Feature;

use App\Data\Rpc\ValidateAddressData;
use App\Models\AddressBalance;
use App\Services\PepecoinExplorerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewAddressPageTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function invalid_address_returns_404(): void
    {
        // Test with an invalid character (contains '0' which is not allowed by route regex)
        // Route returns 404 when address doesn't match the pattern
        $this->get('/address/PEPEaddress1234567890ABCDEFGHIJ')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function valid_address_not_in_db_and_not_ours_shows_coming_soon(): void
    {
        // Valid address matching the route's regex pattern (no 0, O, I, l)
        $address = 'PEPEaddress123456789ABCDEFGHiJ'; // 30 chars, valid format

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        $explorer->shouldReceive('validateAddress')
            ->once()
            ->with($address)
            ->andReturn(ValidateAddressData::from([
                'isvalid' => true,
                'ismine' => false,
                'address' => $address,
            ]));
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('address.show', ['address' => $address]))
            ->assertOk()
            ->assertSee($address);
    }

    #[Test]
    public function address_present_in_db_renders_balances_and_transactions(): void
    {
        $address = 'PEPEaddress123456789ABCDEFGHiJ'; // 30 chars, valid format

        AddressBalance::factory()->create([
            'address' => $address,
            'balance' => 12.34,
            'total_received' => 56.78,
            'total_sent' => 44.44,
            'tx_count' => 2,
        ]);

        $explorer = Mockery::mock(PepecoinExplorerService::class);
        // The controller should not call validateAddress in DB path; ensure no calls
        $this->app->instance(PepecoinExplorerService::class, $explorer);

        $this->get(route('address.show', ['address' => $address]))
            ->assertOk()
            ->assertSee($address)
            ->assertSee('Transactions')
            ->assertSee('Total PEPE Received')
            ->assertSee('Total PEPE Sent')
            ->assertSee('PEPE Balance');
    }
}
