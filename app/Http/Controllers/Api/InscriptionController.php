<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Services\OrdinalsService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class InscriptionController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly OrdinalsService $ordinals,
    ) {}

    public function index(): JsonResponse
    {
        $inscriptions = Inscription::query()
            ->where(function ($query) {
                $query->where('content_type', 'like', 'image/%')
                    ->orWhere('content_type', 'like', 'text/html%');
            })
            ->orderByDesc('id')
            ->limit(60)
            ->pluck('inscription_id');

        return response()->json([
            'inscriptions' => $inscriptions,
            'total' => $inscriptions->count(),
        ]);
    }

    public function show(string $inscriptionId): JsonResponse
    {
        if (! $this->isValidInscriptionId($inscriptionId)) {
            return $this->invalidInscriptionIdResponse();
        }

        try {
            return response()->json($this->ordinals->getInscription($inscriptionId));
        } catch (RequestException $e) {
            if ($e->response->status() === Response::HTTP_NOT_FOUND) {
                return $this->inscriptionNotFoundResponse();
            }

            throw $e;
        } catch (Throwable $e) {
            if (str_contains(strtolower($e->getMessage()), 'not found')) {
                return $this->inscriptionNotFoundResponse();
            }

            throw $e;
        }
    }

    private function isValidInscriptionId(string $id): bool
    {
        // Format: 64-char hex txid + 'i' + numeric index
        return (bool) preg_match('/^[0-9a-fA-F]{64}i\d+$/', $id);
    }

    private function invalidInscriptionIdResponse(): JsonResponse
    {
        return $this->errorResponse(
            'invalid_inscription_id',
            'The provided inscription ID is invalid. Expected format: <txid>i<index>',
            Response::HTTP_BAD_REQUEST
        );
    }

    private function inscriptionNotFoundResponse(): JsonResponse
    {
        return $this->errorResponse(
            'inscription_not_found',
            'The requested inscription could not be found.',
            Response::HTTP_NOT_FOUND
        );
    }
}
