<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MempoolController extends Controller
{
    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function index(): JsonResponse
    {
        $mempool = $this->blockchain->getMempool();

        return new JsonResponse([
            'count' => $mempool->count,
            'vsize' => $mempool->vsize,
            'total_fee' => $mempool->getTotalFeeInPep(),
        ]);
    }

    public function txids(): JsonResponse
    {
        return new JsonResponse($this->blockchain->getMempoolTxIds());
    }

    public function recent(): JsonResponse
    {
        return new JsonResponse($this->blockchain->getRecentMempoolTransactions());
    }

    public function feeEstimates(): JsonResponse
    {
        return new JsonResponse($this->blockchain->getFeeEstimates());
    }
}
