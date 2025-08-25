<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Services\PepecoinExplorerService;
use App\Services\PepecoinRpcService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(PepecoinRpcService $rpc, PepecoinExplorerService $explorer): View
    {
        try {
            return view('homepage', [
                'blockchain' => $rpc->getBlockchainInfo(),
                'mempool' => $explorer->getMempoolInfo(),
                'network' => $rpc->getNetworkInfo(),
                'latestBlocks' => Block::getLatestBlocks(),
                'mempoolTransactions' => $explorer->getMempoolTxIds()->take(10),
            ]);

        } catch (Exception $e) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Unable to connect to Pepecoin node: '.$e->getMessage());
        }
    }
}
