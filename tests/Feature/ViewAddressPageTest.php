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

    #[Test]
    public function invalid_address_returns_400(): void
    {
        $this->get(route('address.show', ['address' => 'PTooShort']))
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function valid_address_not_in_db_and_not_ours_shows_coming_soon(): void
    {
        $address = 'PValidAddress1234567890ABCDE'; // 26 chars, matches route pattern

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
        $address = 'PDbAddress1234567890ABCDEFG';

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
