<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\BlockchainServiceInterface;
use App\Models\PoolStat;
use App\Services\PepecoinExplorerService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Number;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(PepecoinExplorerService $explorer, BlockchainServiceInterface $blockchain): View
    {
        try {
            // Use the calculated 24h average if available, fallback to real-time estimate
            $hashrateValue = PoolStat::getLatestNetworkHashrate() ?: $explorer->getHashrate();

            return view('homepage', [
                'mempool' => $blockchain->getMempool(),
                'network' => [
                    'subversion' => $explorer->getNetworkSubversion(),
                    'connectionCount' => $explorer->getNetworkConnectionsCount(),
                ],
                'chainSize' => Number::fileSize($explorer->getChainSize(), precision: 1),
                'blockHeight' => $blockchain->getBlockTipHeight(),
                'difficulty' => format_difficulty($explorer->getDifficulty()),
                'hashrate' => format_hashrate($hashrateValue),
            ]);

        } catch (Exception $e) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Unable to connect to Pepecoin node: '.$e->getMessage());
        }
    }
}
