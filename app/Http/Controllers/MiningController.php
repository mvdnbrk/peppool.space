<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pool;
use App\Services\PepecoinExplorerService;
use Illuminate\View\View;

class MiningController extends Controller
{
    public function index(PepecoinExplorerService $explorer): View
    {
        return view('mining.dashboard', [
            'network' => [
                'subversion' => $explorer->getNetworkSubversion(),
                'connectionCount' => $explorer->getNetworkConnectionsCount(),
            ],
        ]);
    }

    public function show(string $slug, PepecoinExplorerService $explorer): View
    {
        $pool = Pool::where('slug', $slug)->firstOrFail();

        return view('mining.pool', [
            'pool' => $pool,
            'network' => [
                'subversion' => $explorer->getNetworkSubversion(),
                'connectionCount' => $explorer->getNetworkConnectionsCount(),
            ],
        ]);
    }
}
