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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pepe:sync:nodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync peer nodes from the Pepecoin RPC and resolve their geographic locations';

    public function __construct(
        private readonly PepecoinRpcService $rpcService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting global node synchronization...');

        Node::query()->update(['is_online' => false]);

        $allPeers = array_merge($this->fetchLocal(), $this->fetchRemote());

        $uniquePeers = collect($allPeers)->unique('addr');
        $this->info("Total unique peers found: {$uniquePeers->count()}");

        $synchronized = 0;
        $localized = 0;

        foreach ($uniquePeers as $peerData) {
            $parsed = $this->parsePeerAddress($peerData['addr']);

            $node = Node::firstOrNew(['ip' => $parsed['ip']]);
            $node->port = $parsed['port'];
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

    /**
     * Fetch peers from the local RPC node.
     */
    private function fetchLocal(): array
    {
        $this->info('Fetching peers from Local RPC...');

        try {
            return $this->rpcService->call('getpeerinfo');
        } catch (\Exception $e) {
            $this->error("Failed to fetch local peers: {$e->getMessage()}");

            return [];
        }
    }

    /**
     * Fetch peers from all configured remote nodes via SSH.
     */
    private function fetchRemote(): array
    {
        $allRemotePeers = [];
        $remoteNodes = config('pepecoin.remote_nodes', []);

        foreach ($remoteNodes as $node) {
            if (empty($node['ip']) || empty($node['ssh_user']) || empty($node['cli_path'])) {
                continue;
            }

            $this->info("Fetching peers from remote node: {$node['name']}...");

            $sshCommand = sprintf(
                'ssh -p %d %s@%s "%s getpeerinfo"',
                $node['ssh_port'],
                $node['ssh_user'],
                $node['ip'],
                $node['cli_path']
            );

            $result = Process::run($sshCommand);

            if ($result->failed()) {
                $this->warn("Failed to retrieve peers from {$node['name']}: {$result->errorOutput()}");

                continue;
            }

            $peers = json_decode($result->output(), true);

            if (! is_array($peers)) {
                $this->warn("Failed to decode peers from {$node['name']}.");

                continue;
            }

            $allRemotePeers = array_merge($allRemotePeers, $peers);
            $this->info('Found '.count($peers)." peers on {$node['name']}.");
        }

        return $allRemotePeers;
    }

    /**
     * Resolve and update geographic location for a node.
     */
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
                usleep(500000);

                return true;
            }
        } catch (\Exception $e) {
            $this->warn("Failed to localize IP: {$node->ip} ({$e->getMessage()})");
        }

        return false;
    }

    /**
     * Localize any remaining nodes in the database missing location data.
     */
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

    /**
     * Parse a peer address string into IP and Port components.
     */
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
