<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlockResource;
use App\Models\Block;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorerService
    ) {}

    public function tipHeight(): Response
    {
        return new Response(
            content: (string) $this->explorerService->getBlockTipHeight(),
            headers: ['Content-Type' => 'text/plain']
        );
    }

    public function tipHash(): Response
    {
        return new Response(
            content: $this->explorerService->getBlockTipHash(),
            headers: ['Content-Type' => 'text/plain']
        );
    }

    public function list(?string $startHeight = null): JsonResponse
    {
        $heightFilter = null;

        if ($startHeight !== null) {
            if (! ctype_digit($startHeight)) {
                return new JsonResponse([
                    'error' => 'Bad request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ], Response::HTTP_BAD_REQUEST);
            }

            $heightFilter = (int) $startHeight;
            $exists = Block::query()->where('height', $heightFilter)->exists();
            if (! $exists) {
                return new JsonResponse([
                    'error' => 'Block not found',
                    'code' => Response::HTTP_NOT_FOUND,
                ], Response::HTTP_NOT_FOUND);
            }
        }

        return BlockResource::collection(
            Block::query()
                ->select(['height', 'hash', 'version', 'created_at', 'tx_count', 'size', 'difficulty', 'nonce', 'merkleroot'])
                ->when($heightFilter !== null, fn ($q) => $q->where('height', '<=', $heightFilter))
                ->orderByDesc('height')
                ->limit(10)
                ->get()
        )->response();
    }
}
