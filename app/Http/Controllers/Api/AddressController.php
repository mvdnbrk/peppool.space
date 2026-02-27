<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Exceptions\RpcResponseException;
use App\Exceptions\UnsupportedOperationException;
use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\Client\RequestException;
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
        return $this->handleRequest($address, fn ($addr) => response()->json($this->blockchain->getAddress($addr)));
    }

    public function transactions(string $address): JsonResponse
    {
        return $this->handleRequest($address, fn ($addr) => response()->json($this->blockchain->getAddressTransactions($addr)));
    }

    public function utxo(string $address): JsonResponse
    {
        return $this->handleRequest($address, fn ($addr) => response()->json($this->blockchain->getAddressUtxos($addr)));
    }

    public function validate(string $address): JsonResponse
    {
        return response()->json($this->explorer->validateAddress($address));
    }

    private function handleRequest(string $address, callable $callback): JsonResponse
    {
        try {
            return $callback($address);
        } catch (UnsupportedOperationException $e) {
            return $this->errorResponse('electrs_required', $e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (RequestException $e) {
            return $this->handleNetworkException($e);
        } catch (RpcResponseException $e) {
            return $this->handleRpcException($e);
        } catch (Throwable $e) {
            return $this->handleGenericException($e);
        }
    }

    private function handleGenericException(Throwable $e): JsonResponse
    {
        $status = (int) $e->getCode();

        if ($status === Response::HTTP_BAD_REQUEST || str_contains(strtolower($e->getMessage()), 'invalid')) {
            return $this->invalidAddressResponse();
        }

        if ($status === Response::HTTP_NOT_FOUND || str_contains(strtolower($e->getMessage()), 'not found')) {
            return $this->addressNotFoundResponse();
        }

        throw $e;
    }

    private function handleNetworkException(RequestException $e): JsonResponse
    {
        if ($e->getCode() === Response::HTTP_BAD_REQUEST) {
            return $this->invalidAddressResponse();
        }

        if ($e->getCode() === Response::HTTP_NOT_FOUND) {
            return $this->addressNotFoundResponse();
        }

        throw $e;
    }

    private function handleRpcException(RpcResponseException $e): JsonResponse
    {
        if ($e->httpStatus === Response::HTTP_BAD_REQUEST) {
            return $this->invalidAddressResponse();
        }

        if ($e->httpStatus === Response::HTTP_NOT_FOUND) {
            return $this->addressNotFoundResponse();
        }

        throw $e;
    }
}
