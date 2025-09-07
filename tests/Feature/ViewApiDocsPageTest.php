<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewApiDocsPageTest extends TestCase
{
    #[Test]
    public function api_docs_page_can_be_viewed(): void
    {
        $this->get(route('docs.api'))
            ->assertOk()
            ->assertViewIs('docs.api')
            ->assertSee('Pepecoin API Documentation')
            ->assertSee('Base URL')
            ->assertSee('Rate Limiting');
    }

    #[Test]
    public function api_docs_page_lists_key_endpoints(): void
    {
        $this->get(route('docs.api'))
            ->assertOk()
            ->assertSee('/blocks/tip/hash')
            ->assertSee('/blocks/tip/height')
            ->assertSee('/mempool')
            ->assertSee('/mempool/txids')
            ->assertSee('/prices')
            ->assertSee('/validate-address/:address');
    }
}
