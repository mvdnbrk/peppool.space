<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewWalletPageTest extends TestCase
{
    #[Test]
    public function wallet_page_can_be_viewed(): void
    {
        $this->get(route('wallet'))
            ->assertStatus(200)
            ->assertViewIs('wallet')
            ->assertSee('Peppool Wallet: The Pepecoin wallet for everyone. (coming soon)')
            ->assertSee('Coming Soon')
            ->assertSee('Sneak peek')
            ->assertSee('wallet-preview/1-dashboard-frame.png')
            ->assertSee('Browser Extension')
            ->assertSee('Secure & Open', false)
            ->assertSee('Easy to Use');
    }

    #[Test]
    public function wallet_page_contains_links(): void
    {
        $this->get(route('wallet'))
            ->assertStatus(200)
            ->assertSee('View on GitHub');
    }
}
