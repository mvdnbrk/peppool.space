<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;

class MempoolController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorerService
    ) {}

    public function index(): JsonResponse
    {
        $mempool = $this->explorerService->getMempoolInfo();

        return new JsonResponse([
            'count' => $mempool->size,
            'bytes' => $mempool->bytes,
        ]);
    }

    public function txids(): JsonResponse
    {
        return new JsonResponse(
            $this->explorerService->getMempoolTxIds()
        );
    }
}
