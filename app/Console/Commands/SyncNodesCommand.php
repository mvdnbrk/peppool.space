<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Services\Location\IpApiFull;
use App\Services\PepecoinRpcService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Process;
use Stevebauman\Location\Facades\Location;

class SyncNodesCommand extends Command
{
    protected $signature = 'pepe:sync:nodes';

    protected $description = 'Sync peer nodes from the Pepecoin RPC and resolve their geographic locations';

    public function __construct(
        private readonly PepecoinRpcService $rpcService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting global node synchronization...');

        Node::query()->update(['is_online' => false]);

        $allPeers = array_merge($this->fetchLocal(), $this->fetchRemote());

        // Pre-parse addresses to get IPs for grouping
        $processedPeers = collect($allPeers)->map(function ($peer) {
            $parsed = $this->parsePeerAddress($peer['addr']);
            $peer['ip'] = $parsed['ip'];
            $peer['port'] = $parsed['port'];

            return $peer;
        });

        $groupedPeers = $processedPeers->groupBy('ip');
        $this->info("Total unique peers found: {$groupedPeers->count()}");

        $synchronized = 0;
        $localized = 0;

        foreach ($groupedPeers as $ip => $peerGroup) {
            $peerData = $peerGroup->first();
            $newSources = $peerGroup->pluck('source')->unique()->sort()->values()->all();

            $node = Node::firstOrNew(['ip' => $ip]);
            $node->sources = $newSources;
            $node->port = $peerData['port'];
            $node->version = $peerData['version'] ?? null;
            $node->subversion = $peerData['subver'] ?? null;
            $node->is_online = true;
            $node->last_seen_at = Carbon::now();

            if (! $node->country_code && $this->fetchGeolocation($node)) {
                $localized++;
            }

            $node->save();
            $synchronized++;
        }

        $this->info("Successfully synchronized {$synchronized} nodes ({$localized} newly localized).");

        $this->backfillLocationData();

        return self::SUCCESS;
    }

    private function fetchLocal(): array
    {
        $this->info('Fetching peers from Local RPC...');

        try {
            $peers = $this->rpcService->call('getpeerinfo');

            return collect($peers)->map(function ($peer) {
                $peer['source'] = 'peppool.space-01';

                return $peer;
            })->toArray();
        } catch (\Exception $e) {
            $this->error("Failed to fetch local peers: {$e->getMessage()}");

            return [];
        }
    }

    private function fetchRemote(): array
    {
        $allRemotePeers = [];
        $remoteNodes = config('pepecoin.remote_nodes', []);

        foreach ($remoteNodes as $index => $node) {
            if (empty($node['ip']) || empty($node['ssh_user']) || empty($node['cli_path'])) {
                continue;
            }

            $nodeName = $node['name'] ?? "node_{$index}";
            $this->info("Fetching peers from remote node: {$nodeName}...");

            $sshCommand = sprintf(
                'ssh -p %d %s@%s "%s getpeerinfo"',
                $node['ssh_port'],
                $node['ssh_user'],
                $node['ip'],
                $node['cli_path']
            );

            $result = Process::run($sshCommand);

            if ($result->failed()) {
                $this->warn("Failed to retrieve peers from {$nodeName}: {$result->errorOutput()}");

                continue;
            }

            $peers = json_decode($result->output(), true);

            if (! is_array($peers)) {
                $this->warn("Failed to decode peers from {$nodeName}.");

                continue;
            }

            $peersWithSource = collect($peers)->map(function ($peer) use ($nodeName) {
                $peer['source'] = $nodeName;

                return $peer;
            })->toArray();

            $allRemotePeers = array_merge($allRemotePeers, $peersWithSource);
            $this->info('Found '.count($peers)." peers on {$nodeName}.");
        }

        return $allRemotePeers;
    }

    private function fetchGeolocation(Node $node): bool
    {
        try {
            if ($location = Location::setDriver(new IpApiFull)->get($node->ip)) {
                $node->fill([
                    'continent' => $location->continentName,
                    'continent_code' => $location->continentCode,
                    'country' => $location->countryName,
                    'country_code' => $location->countryCode,
                    'region' => $location->regionName,
                    'region_code' => $location->regionCode,
                    'city' => $location->cityName,
                    'latitude' => (float) $location->latitude,
                    'longitude' => (float) $location->longitude,
                    'isp' => $location->isp ?? null,
                ]);

                // Avoid hitting free API rate limits (45 req/min)
                if (! app()->runningUnitTests()) {
                    usleep(500000);
                }

                return true;
            }
        } catch (\Exception $e) {
            $this->warn("Failed to localize IP: {$node->ip} ({$e->getMessage()})");
        }

        return false;
    }

    private function backfillLocationData(): void
    {
        $unlocalized = Node::whereNull('country_code')->get();

        if ($unlocalized->isEmpty()) {
            return;
        }

        $this->info("Attempting to localize {$unlocalized->count()} remaining nodes...");

        foreach ($unlocalized as $node) {
            if ($this->fetchGeolocation($node)) {
                $node->save();
                $this->info("Localized: {$node->ip}");
            }
        }
    }

    private function parsePeerAddress(string $addr): array
    {
        if (str_starts_with($addr, '[')) {
            preg_match('/^\[(.*)\]:(\d+)$/', $addr, $matches);

            return [
                'ip' => $matches[1] ?? $addr,
                'port' => (int) ($matches[2] ?? 33874),
            ];
        }

        $parts = explode(':', $addr);

        return [
            'ip' => $parts[0],
            'port' => (int) ($parts[1] ?? 33874),
        ];
    }
}
