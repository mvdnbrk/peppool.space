<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Exceptions\UnsupportedOperationException;
use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class AddressController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly PepecoinExplorerService $explorer,
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function show(string $address): JsonResponse
    {
        try {
            return response()->json($this->blockchain->getAddress($address));
        } catch (UnsupportedOperationException $e) {
            return $this->errorResponse('electrs_required', $e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (Throwable $e) {
            if ($e->getCode() === Response::HTTP_BAD_REQUEST || str_contains(strtolower($e->getMessage()), 'invalid')) {
                return $this->invalidAddressResponse();
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND || str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->addressNotFoundResponse();
            }

            throw $e;
        }
    }

    public function transactions(string $address): JsonResponse
    {
        try {
            return response()->json($this->blockchain->getAddressTransactions($address));
        } catch (UnsupportedOperationException $e) {
            return $this->errorResponse('electrs_required', $e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (Throwable $e) {
            if ($e->getCode() === Response::HTTP_BAD_REQUEST || str_contains(strtolower($e->getMessage()), 'invalid')) {
                return $this->invalidAddressResponse();
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND || str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->addressNotFoundResponse();
            }

            throw $e;
        }
    }

    public function utxo(string $address): JsonResponse
    {
        try {
            return response()->json($this->blockchain->getAddressUtxos($address));
        } catch (UnsupportedOperationException $e) {
            return $this->errorResponse('electrs_required', $e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (Throwable $e) {
            if ($e->getCode() === Response::HTTP_BAD_REQUEST || str_contains(strtolower($e->getMessage()), 'invalid')) {
                return $this->invalidAddressResponse();
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND || str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->addressNotFoundResponse();
            }

            throw $e;
        }
    }

    public function validate(string $address): JsonResponse
    {
        return response()->json($this->explorer->validateAddress($address));
    }
}
