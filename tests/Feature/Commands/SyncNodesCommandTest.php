<?php

namespace Tests\Feature\Commands;

use App\Models\Node;
use App\Services\PepecoinRpcService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;
use Tests\TestCase;

class SyncNodesCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_syncs_nodes_from_local_rpc(): void
    {
        $rpcMock = Mockery::mock(PepecoinRpcService::class);
        $rpcMock->shouldReceive('call')
            ->with('getpeerinfo')
            ->andReturn([
                [
                    'addr' => '1.2.3.4:33874',
                    'version' => 70016,
                    'subver' => '/pepetoshi:1.1.0/',
                ]
            ]);
        $this->app->instance(PepecoinRpcService::class, $rpcMock);

        // Mock Location
        $position = new Position();
        $position->countryName = 'Netherlands';
        $position->countryCode = 'NL';
        $position->continentName = 'Europe';
        $position->continentCode = 'EU';
        $position->regionName = 'North Holland';
        $position->regionCode = 'NH';
        $position->cityName = 'Haarlem';
        $position->latitude = '52.3695';
        $position->longitude = '4.6359';
        $position->isp = 'KPN';

        Location::shouldReceive('setDriver')->andReturnSelf();
        Location::shouldReceive('get')->andReturn($position);

        $this->artisan('pepe:sync:nodes')
            ->assertSuccessful();

        $this->assertDatabaseHas('nodes', [
            'ip' => '1.2.3.4',
            'country' => 'Netherlands',
            'is_online' => true,
        ]);

        $node = Node::where('ip', '1.2.3.4')->first();
        $this->assertEquals(['peppool.space-01'], $node->sources);
    }

    #[Test]
    public function it_syncs_nodes_from_remote_nodes_via_ssh(): void
    {
        // Empty local peers
        $rpcMock = Mockery::mock(PepecoinRpcService::class);
        $rpcMock->shouldReceive('call')->with('getpeerinfo')->andReturn([]);
        $this->app->instance(PepecoinRpcService::class, $rpcMock);

        // Setup remote node config
        Config::set('pepecoin.remote_nodes', [
            [
                'name' => 'remote-node',
                'ip' => '5.6.7.8',
                'ssh_user' => 'ploi',
                'ssh_port' => 22,
                'cli_path' => 'pepecoin-cli',
            ]
        ]);

        // Mock Process for SSH
        Process::fake([
            '*' => Process::result(json_encode([
                [
                    'addr' => '9.10.11.12:33874',
                    'version' => 70016,
                    'subver' => '/pepetoshi:1.1.0/',
                ]
            ])),
        ]);

        // Mock Location
        $position = new Position();
        $position->countryName = 'United States';
        $position->countryCode = 'US';
        $position->continentName = 'North America';
        $position->continentCode = 'NA';
        
        Location::shouldReceive('setDriver')->andReturnSelf();
        Location::shouldReceive('get')->andReturn($position);

        $this->artisan('pepe:sync:nodes')
            ->assertSuccessful();

        $this->assertDatabaseHas('nodes', [
            'ip' => '9.10.11.12',
            'country' => 'United States',
            'is_online' => true,
        ]);

        $node = Node::where('ip', '9.10.11.12')->first();
        $this->assertEquals(['remote-node'], $node->sources);
    }
}
