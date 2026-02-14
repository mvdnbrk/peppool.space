<?php

namespace App\Http\Controllers\Api;

use App\Data\Rpc\ValidateAddressData;
use App\Http\Controllers\Controller;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorer,
        private readonly ElectrsPepeService $electrs,
    ) {}

    public function show(string $address): mixed
    {
        try {
            return $this->electrs->getAddress($address);
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
                return response('Invalid address', Response::HTTP_BAD_REQUEST)
                    ->header('Content-Type', 'text/plain');
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return response('Address not found', Response::HTTP_NOT_FOUND)
                    ->header('Content-Type', 'text/plain');
            }

            throw $e;
        }
    }

    public function validate(string $address): ValidateAddressData
    {
        return $this->explorer->validateAddress($address);
    }
}
