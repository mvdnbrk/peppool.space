<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Services\OrdinalsService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class OutputController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly OrdinalsService $ordinals,
    ) {}

    public function show(string $outpoint): JsonResponse
    {
        if (! $this->isValidOutpoint($outpoint)) {
            return $this->invalidOutpointResponse();
        }

        try {
            $output = $this->ordinals->getOutput($outpoint);

            if (! $output->indexed) {
                return $this->outputNotFoundResponse();
            }

            return response()->json($output);
        } catch (RequestException $e) {
            if ($e->response->status() === Response::HTTP_NOT_FOUND) {
                return $this->outputNotFoundResponse();
            }

            throw $e;
        } catch (Throwable $e) {
            if (str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->outputNotFoundResponse();
            }

            throw $e;
        }
    }

    private function isValidOutpoint(string $outpoint): bool
    {
        return (bool) preg_match('/^[0-9a-fA-F]{64}:\d+$/', $outpoint);
    }

    private function invalidOutpointResponse(): JsonResponse
    {
        return $this->errorResponse(
            'invalid_outpoint',
            'The provided outpoint is invalid. Expected format: <txid>:<vout>',
            Response::HTTP_BAD_REQUEST
        );
    }

    private function outputNotFoundResponse(): JsonResponse
    {
        return $this->errorResponse(
            'output_not_found',
            'The requested output could not be found.',
            Response::HTTP_NOT_FOUND
        );
    }
}
