<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PepecoinExplorerService;
use Illuminate\View\View;

class MiningDashboardController extends Controller
{
    public function __invoke(PepecoinExplorerService $explorer): View
    {
        return view('mining.dashboard', [
            'network' => [
                'subversion' => $explorer->getNetworkSubversion(),
                'connectionCount' => $explorer->getNetworkConnectionsCount(),
            ],
        ]);
    }
}
