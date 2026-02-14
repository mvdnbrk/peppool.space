<?php

namespace App\Http\Controllers\Api;

use App\Data\Rpc\ValidateAddressData;
use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    use HasApiResponses;

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
                return $this->invalidAddressResponse();
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->addressNotFoundResponse();
            }

            throw $e;
        }
    }

    public function transactions(string $address): mixed
    {
        try {
            return $this->electrs->getAddressTransactions($address);
        } catch (RequestException $e) {
            if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
                return $this->invalidAddressResponse();
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                return $this->addressNotFoundResponse();
            }

            throw $e;
        }
    }

    public function validate(string $address): ValidateAddressData
    {
        return $this->explorer->validateAddress($address);
    }
}
