<?php

namespace Tests\Feature;

use Tests\TestCase;

class ViewSearchPageTest extends TestCase
{
    public function test_search_page_can_be_viewed(): void
    {
        $this->get('/search')
            ->assertStatus(200)
            ->assertSee('Search the Pepecoin blockchain')
            ->assertSee('Search block height/hash, transaction ID, or address');
    }

    public function test_empty_query_redirects_with_error(): void
    {
        $this->post('/search', ['q' => ''])
            ->assertRedirect(route('search.index'))
            ->assertSessionHas('error', 'Please enter a search term.');
    }
}
