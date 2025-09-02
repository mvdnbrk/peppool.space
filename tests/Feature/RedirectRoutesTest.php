<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RedirectRoutesTest extends TestCase
{
    #[Test]
    public function api_redirects_to_docs_api(): void
    {
        $response = $this->get('/api')
            ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
            ->assertRedirect('/docs/api');
    }

    #[Test]
    public function chart_redirects_to_pepecoin_price(): void
    {
        $response = $this->get('/chart')
            ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
            ->assertRedirect('/pepecoin-price');
    }

    #[Test]
    public function charts_redirects_to_pepecoin_price(): void
    {
        $response = $this->get('/charts')
            ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
            ->assertRedirect('/pepecoin-price');
    }

    #[Test]
    public function price_redirects_to_pepecoin_price(): void
    {
        $response = $this->get('/price')
            ->assertStatus(Response::HTTP_MOVED_PERMANENTLY)
            ->assertRedirect('/pepecoin-price');
    }
}
