<?php

namespace Tests\Feature;

use App\Models\Node;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ViewNetworkStatusPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function network_status_page_can_be_viewed(): void
    {
        Node::factory()->create([
            'ip' => '1.2.3.4',
            'is_online' => true,
            'country' => 'Netherlands',
            'country_code' => 'NL',
            'subversion' => '/pepetoshi:1.1.0/',
            'version' => 70016,
            'sources' => ['local'],
        ]);

        Node::factory()->create([
            'ip' => '5.6.7.8',
            'is_online' => true,
            'country' => 'United States',
            'country_code' => 'US',
            'subversion' => '/Node:0.18.0/',
            'version' => 70015,
            'sources' => ['peppool.space-02'],
        ]);

        $this->get(route('network.index'))
            ->assertOk()
            ->assertSee('Network Status')
            ->assertSee('Netherlands')
            ->assertSee('United States')
            ->assertSee('Pepetoshi 1.1.0')
            ->assertSee('Node 0.18.0')
            ->assertSee('local')
            ->assertSee('peppool.space-02')
            ->assertSee('Live Connections');
    }

    #[Test]
    public function it_only_shows_online_nodes_in_the_table(): void
    {
        Node::factory()->create([
            'ip' => '1.1.1.1',
            'is_online' => true,
            'country' => 'Germany',
        ]);

        Node::factory()->create([
            'ip' => '2.2.2.2',
            'is_online' => false,
            'country' => 'France',
        ]);

        $this->get(route('network.index'))
            ->assertOk()
            ->assertSee('Germany')
            ->assertDontSee('France');
    }
}
