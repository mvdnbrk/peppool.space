<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Node;
use Illuminate\View\View;

class NetworkController extends Controller
{
    public function __invoke(): View
    {
        $nodes = Node::where('is_online', true)->orderBy('last_seen_at', 'desc')->get();

        $stats = collect([
            'total' => Node::count(),
            'online' => $nodes->count(),
            'countries' => $nodes->groupBy('country')->map->count()->sortDesc()->take(5),
            'subversions' => $nodes->groupBy('subversion')->map->count()->sortDesc()->take(5),
            'version' => $nodes->groupBy('version')->map->count()->sortDesc()->keys()->first() ?? 'N/A',
        ]);

        return view('network.index', [
            'nodes' => $nodes,
            'stats' => $stats,
        ]);
    }
}
