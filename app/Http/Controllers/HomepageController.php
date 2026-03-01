<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\BlockchainServiceInterface;
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
            return view('homepage', [
                'mempool' => $blockchain->getMempool(),
                'chainSize' => Number::fileSize($explorer->getChainSize(), precision: 1),
                'blockHeight' => $blockchain->getBlockTipHeight(),
                'difficulty' => format_difficulty($explorer->getDifficulty()),
                'hashrate' => format_hashrate($explorer->getHashrate()),
            ]);

        } catch (Exception $e) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Unable to connect to Pepecoin node: '.$e->getMessage());
        }
    }
}
