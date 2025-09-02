<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MetaDescriptionTest extends TestCase
{
    // Note: We intentionally avoid DB-dependent pages here.

    #[Test]
    public function api_docs_includes_og_description_meta_tags(): void
    {
        $expected = 'Pepecoin API documentation: endpoints for blocks, mempool, prices and more. Includes rate limits, examples and response formats.';

        $this->get(route('docs.api'))
            ->assertOk()
            ->assertSee('<meta name="description" content="' . $expected . '">', false)
            ->assertSee('<meta property="og:description" content="' . $expected . '">', false)
            ->assertSee('<meta property="twitter:description" content="' . $expected . '">', false);
    }

    #[Test]
    public function search_page_includes_og_description_meta_tags(): void
    {
        $expected = 'Search the Pepecoin blockchain: find blocks, transactions, and addresses quickly on peppool.space.';

        $this->get(route('search.index'))
            ->assertOk()
            ->assertSee('<meta name="description" content="' . $expected . '">', false)
            ->assertSee('<meta property="og:description" content="' . $expected . '">', false)
            ->assertSee('<meta property="twitter:description" content="' . $expected . '">', false);
    }
}
