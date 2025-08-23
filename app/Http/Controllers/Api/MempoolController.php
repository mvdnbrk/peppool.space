<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;

class MempoolController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorerService
    ) {}

    public function index(): JsonResponse
    {
        return new JsonResponse(
            $this->explorerService->getMempoolInfo()
                ->only(['size', 'bytes'])
                ->mapWithKeys(fn ($value, $key) => [
                    $key === 'size' ? 'count' : $key => $value
                ])
        );
    }
}
