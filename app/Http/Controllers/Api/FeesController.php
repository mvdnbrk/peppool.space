<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;

class FeesController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorer
    ) {}

    public function recommended(): JsonResponse
    {
        $fees = $this->explorer->getRecommendedFees();

        return response()->json(array_map(fn ($f) => (int) round($f), $fees));
    }

    public function precise(): JsonResponse
    {
        return response()->json($this->explorer->getRecommendedFees());
    }
}
