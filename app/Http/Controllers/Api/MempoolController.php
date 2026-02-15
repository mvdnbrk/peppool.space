<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;

class MempoolController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorerService,
        private readonly ElectrsPepeService $electrs
    ) {}

    public function index(): JsonResponse
    {
        try {
            $mempool = $this->electrs->getMempool();

            return new JsonResponse([
                'count' => $mempool->count,
                'vsize' => $mempool->vsize,
                'total_fee' => $mempool->getTotalFeeInPep(),
            ]);
        } catch (\Exception $e) {
            $mempool = $this->explorerService->getMempoolInfo();

            return new JsonResponse([
                'count' => $mempool->size,
                'bytes' => $mempool->bytes,
            ]);
        }
    }

    public function txids(): JsonResponse
    {
        return new JsonResponse(
            $this->explorerService->getMempoolTxIds()
        );
    }

    public function recent(): JsonResponse
    {
        return new JsonResponse(
            $this->electrs->getRecentMempoolTransactions()
        );
    }

    public function feeEstimates(): JsonResponse
    {
        $estimates = [];

        try {
            $estimates = $this->electrs->getFeeEstimates();
        } catch (\Exception) {
            // Fallback to RPC
        }

        if (empty($estimates)) {
            $estimates = $this->explorerService->getFeeEstimates();
        }

        return new JsonResponse($estimates);
    }
}
