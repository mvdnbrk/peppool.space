<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function __construct(private readonly PepecoinExplorerService $explorer) {}

    public function validate(string $address): JsonResponse
    {
        $data = $this->explorer->validateAddress($address);

        return response()->json([
            'isvalid' => (bool) $data->get('isvalid', false),
            'address' => (string) $data->get('address', $address),
            'scriptPubKey' => $data->get('scriptPubKey'),
            'isscript' => (bool) $data->get('isscript', false),
        ]);
    }
}
