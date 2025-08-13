<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Services\PepecoinRpcService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(Request $request, PepecoinRpcService $rpc): View
    {
        try {
            $blockchainInfo = $rpc->getBlockchainInfo();
            $mempoolInfo = $rpc->getMempoolInfo();
            $networkInfo = $rpc->getNetworkInfo();

            return view('homepage', [
                'blockchain' => $blockchainInfo,
                'mempool' => $mempoolInfo,
                'network' => $networkInfo,
                'latestBlocks' => Block::getLatestBlocks(),
                'mempoolTransactions' => array_slice($rpc->getRawMempool(false), 0, 20),
            ]);

        } catch (\Exception $e) {
            return view('homepage', [
                'error' => 'Unable to connect to Pepecoin node: '.$e->getMessage(),
                'blockchain' => null,
                'mempool' => null,
                'network' => null,
                'latestBlocks' => [],
                'mempoolTransactions' => [],
            ]);
        }
    }
}
