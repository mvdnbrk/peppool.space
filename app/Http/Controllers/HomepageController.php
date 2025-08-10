<?php

namespace App\Http\Controllers;

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

            // Get latest blocks
            $currentHeight = $blockchainInfo['blocks'];
            $latestBlocks = [];

            for ($i = 0; $i < 10; $i++) {
                $height = $currentHeight - $i;
                if ($height < 0) {
                    break;
                }

                $blockHash = $rpc->getBlockHash($height);
                $block = $rpc->getBlock($blockHash, 1);

                $latestBlocks[] = [
                    'height' => $height,
                    'hash' => $blockHash,
                    'time' => $block['time'],
                    'tx_count' => count($block['tx'] ?? []),
                    'size' => $block['size'] ?? 0,
                ];
            }

            // Get mempool transactions
            $mempoolTxs = $rpc->getRawMempool(false);
            $mempoolTransactions = array_slice($mempoolTxs, 0, 20); // Show first 20

            return view('homepage', [
                'blockchain' => $blockchainInfo,
                'mempool' => $mempoolInfo,
                'network' => $networkInfo,
                'latestBlocks' => $latestBlocks,
                'mempoolTransactions' => $mempoolTransactions,
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
