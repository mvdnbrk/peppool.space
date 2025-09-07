<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(private readonly PepecoinExplorerService $explorer) {}

    public function validate(Request $request, string $address): JsonResponse
    {
        return $this->explorer
            ->validateAddress($address)
            ->toResponse($request);
    }
}
