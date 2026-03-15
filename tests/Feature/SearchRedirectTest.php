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

    #[Test]
    public function search_redirects_for_inscription_id_with_i_separator(): void
    {
        $inscriptionId = '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0';

        $this->post(route('search.store'), ['q' => $inscriptionId])
            ->assertRedirect(route('inscription.show', ['inscriptionId' => $inscriptionId]));
    }

    #[Test]
    public function search_redirects_for_inscription_id_with_colon_separator(): void
    {
        $this->post(route('search.store'), ['q' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3:0'])
            ->assertRedirect(route('inscription.show', ['inscriptionId' => '5f48e29e693d92b1ba70f306b1fb4fb5a5dd2b272dd6130ab2df46ab4875e2f3i0']));
    }
}
