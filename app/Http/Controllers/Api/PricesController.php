<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;

class PricesController extends Controller
{
    public function __invoke(PepecoinExplorerService $explorerService): JsonResponse
    {
        return new JsonResponse(
            $explorerService->getPrices()
        );
    }
}
