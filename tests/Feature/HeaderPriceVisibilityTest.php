<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HeaderPriceVisibilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_pepe_price_and_hides_generic_price_link_when_price_is_cached(): void
    {
        Cache::put('pepecoin_price_usd', 0.00012345);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<span class="text-green-700 font-medium text-xs sm:text-sm">PEPE</span>', false)
            ->assertSee('0.00012345')
            ->assertSee('data-vue="pepe-price"', false)
            ->assertDontSee('<span class="text-green-700 group-hover:text-green-800 font-medium text-xs sm:text-sm">Price</span>', false);
    }

    #[Test]
    public function it_hides_pepe_price_and_shows_generic_price_link_when_price_is_not_cached(): void
    {
        Cache::forget('pepecoin_price_usd');

        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('<span class="text-green-700 font-medium text-xs sm:text-sm">PEPE</span>', false)
            ->assertDontSee('data-vue="pepe-price"', false)
            ->assertSee('<span class="text-green-700 group-hover:text-green-800 font-medium text-xs sm:text-sm">Price</span>', false);
    }

    #[Test]
    public function it_hides_pepe_price_and_shows_generic_price_link_when_show_pepe_price_is_false(): void
    {
        Cache::put('pepecoin_price_usd', 0.00012345);

        // We can test this on a page where showPepePrice is explicitly set to false in the layout component
        // Looking at resources/views/price.blade.php:
        // <x-layout title="..." :showPepePrice="false">

        $this->get(route('price'))
            ->assertOk()
            ->assertDontSee('<span class="text-green-700 font-medium text-xs sm:text-sm">PEPE</span>', false)
            ->assertDontSee('data-vue="pepe-price"', false)
            ->assertSee('<span class="text-green-700 group-hover:text-green-800 font-medium text-xs sm:text-sm">Price</span>', false);
    }
}
