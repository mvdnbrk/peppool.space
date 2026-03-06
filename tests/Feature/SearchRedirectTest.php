<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchRedirectTest extends TestCase
{
    #[Test]
    public function search_redirects_for_p2pkh_addresses_starting_with_p(): void
    {
        $address = 'PpnGBQtSTECJg8gx8pB57VzKN7LwEJBKTp';

        $this->post(route('search.store'), ['q' => $address])
            ->assertRedirect(route('address.show', ['address' => $address]));
    }

    #[Test]
    public function search_redirects_for_p2pkh_addresses_starting_with_9(): void
    {
        // Pepecoin testnet P2PKH addresses start with 9
        $address = '9vnGBQtSTECJg8gx8pB57VzKN7LwEJBKTp';

        $this->post(route('search.store'), ['q' => $address])
            ->assertRedirect(route('address.show', ['address' => $address]));
    }

    #[Test]
    public function search_redirects_for_p2sh_addresses_starting_with_a(): void
    {
        $address = 'AAhW5r64xmswWAttZd9WkTwPQZC2f3WPSE';

        $this->post(route('search.store'), ['q' => $address])
            ->assertRedirect(route('address.show', ['address' => $address]));
    }

    #[Test]
    public function search_redirects_for_testnet_p2sh_addresses_starting_with_2(): void
    {
        // Pepecoin testnet P2SH addresses start with 2
        $address = '2vnGBQtSTECJg8gx8pB57VzKN7LwEJBKTp';

        $this->post(route('search.store'), ['q' => $address])
            ->assertRedirect(route('address.show', ['address' => $address]));
    }
}
