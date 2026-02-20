<?php

namespace App\Http\Controllers;

use App\Services\PepecoinExplorerService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Number;
use Illuminate\View\View;

class HomepageController extends Controller
{
    public function __invoke(PepecoinExplorerService $explorer): View
    {
        try {
            return view('homepage', [
                'mempool' => $explorer->getMempoolInfo(),
                'network' => [
                    'subversion' => $explorer->getNetworkSubversion(),
                    'connectionCount' => $explorer->getNetworkConnectionsCount(),
                ],
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
