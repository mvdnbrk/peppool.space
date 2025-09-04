<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinPriceService;
use Illuminate\Http\JsonResponse;

class PricesController extends Controller
{
    public function __invoke(PepecoinPriceService $priceService): JsonResponse
    {
        return new JsonResponse(
            $priceService->getPrices()
        );
    }
}
