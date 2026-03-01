<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Services\Location\IpApiFull;
use App\Services\PepecoinRpcService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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
        $this->info('Fetching peers from RPC...');

        try {
            $peers = $this->rpcService->call('getpeerinfo');
        } catch (\Exception $e) {
            $this->error('Failed to fetch peers: '.$e->getMessage());

            return self::FAILURE;
        }

        // Mark all nodes as offline before updating
        Node::query()->update(['is_online' => false]);

        $count = count($peers);
        $this->info("Processing {$count} peers...");

        $synchronized = 0;
        $localized = 0;

        foreach ($peers as $peer) {
            $addr = $peer['addr'];

            // Handle IPv6 and IPv4 address/port separation
            if (str_starts_with($addr, '[')) {
                // IPv6 [2a01:...] : port
                preg_match('/^\[(.*)\]:(\d+)$/', $addr, $matches);
                $ip = $matches[1] ?? $addr;
                $port = (int) ($matches[2] ?? 33874);
            } else {
                // IPv4 ip : port
                $parts = explode(':', $addr);
                $ip = $parts[0];
                $port = (int) ($parts[1] ?? 33874);
            }

            // Find or create node
            $node = Node::firstOrNew(['ip' => $ip]);

            // Try to resolve location for new nodes or nodes missing location
            if (! $node->exists || ! $node->country_code) {
                // Use our custom driver specifically for this sync
                if ($location = Location::setDriver(new IpApiFull)->get($ip)) {
                    $node->continent = $location->continentName;
                    $node->continent_code = $location->continentCode;
                    $node->country = $location->countryName;
                    $node->country_code = $location->countryCode;
                    $node->region = $location->regionName;
                    $node->region_code = $location->regionCode;
                    $node->city = $location->cityName;
                    $node->latitude = (float) $location->latitude;
                    $node->longitude = (float) $location->longitude;
                    $node->isp = $location->isp ?? null;

                    $localized++;

                    // Add a small delay to avoid hitting the 45-req/min free limit
                    usleep(500000); // 0.5s delay
                } else {
                    $this->warn("Failed to localize IP: {$ip} (could be rate-limited)");
                }
            }

            $node->port = $port;
            $node->version = $peer['version'] ?? null;
            $node->subversion = $peer['subver'] ?? null;
            $node->is_online = true;
            $node->last_seen_at = Carbon::now();
            $node->save();

            $synchronized++;
        }

        $this->info("Successfully synchronized {$synchronized} nodes ({$localized} newly localized).");

        // Now try to localize any remaining nodes in the database that are still missing location data
        $unlocalizedNodes = Node::whereNull('country_code')->get();
        if ($unlocalizedNodes->isNotEmpty()) {
            $this->info("Attempting to localize {$unlocalizedNodes->count()} remaining nodes...");
            foreach ($unlocalizedNodes as $node) {
                if ($location = Location::setDriver(new IpApiFull())->get($node->ip)) {
                    $node->update([
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
                    $this->info("Localized: {$node->ip}");
                    usleep(500000);
                }
            }
        }

        return self::SUCCESS;
    }
}
