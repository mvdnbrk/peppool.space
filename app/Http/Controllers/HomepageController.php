<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use App\Services\PepecoinRpcService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Number;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(PepecoinRpcService $rpc, PepecoinExplorerService $explorer, ElectrsPepeService $electrs): View
    {
        try {
            return view('homepage', [
                'blockchain' => $rpc->getBlockchainInfo(),
                'mempool' => $explorer->getMempoolInfo(),
                'network' => [
                    'subversion' => $explorer->getNetworkSubversion(),
                    'connectionCount' => $explorer->getNetworkConnectionsCount(),
                ],
                'latestBlocks' => Block::getLatestBlocks(),
                'mempoolTransactions' => $electrs->getRecentMempoolTransactions()->take(10),
                'chainSize' => Number::fileSize($explorer->getChainSize(), precision: 1),
                'blockHeight' => $explorer->getBlockTipHeight(),
                'difficulty' => format_difficulty($explorer->getDifficulty()),
                'hashrate' => format_hashrate($explorer->getHashrate()),
            ]);

        } catch (Exception $e) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Unable to connect to Pepecoin node: '.$e->getMessage());
        }
    }
}
