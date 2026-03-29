<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\BlockchainServiceInterface;
use App\Http\Controllers\Api\Concerns\HasApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlockResource;
use App\Models\Block;
use App\Services\OrdinalsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class BlockController extends Controller
{
    use HasApiResponses;

    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
        private readonly OrdinalsService $ordinals,
    ) {}

    public function tipHeight(): Response
    {
        return response((string) $this->blockchain->getBlockTipHeight(), Response::HTTP_OK)
            ->header('Content-Type', 'text/plain');
    }

    public function tipHash(): Response
    {
        return response($this->blockchain->getBlockTipHash(), Response::HTTP_OK)
            ->header('Content-Type', 'text/plain');
    }

    public function inscriptions(string $hashOrHeight): JsonResponse
    {
        try {
            if (! is_numeric($hashOrHeight)) {
                return $this->errorResponse('bad_request', 'Block height must be numeric.', Response::HTTP_BAD_REQUEST);
            }

            return response()->json(collect($this->ordinals->getBlock((int) $hashOrHeight))->only('inscriptions'));
        } catch (Throwable) {
            return response()->json(['inscriptions' => []], Response::HTTP_OK);
        }
    }

    public function list(?string $startHeight = null): JsonResponse
    {
        $heightFilter = null;

        if ($startHeight !== null) {
            if (! ctype_digit($startHeight)) {
                return $this->errorResponse('bad_request', 'The provided startHeight must be an integer.', Response::HTTP_BAD_REQUEST);
            }

            $heightFilter = (int) $startHeight;
            $exists = Block::query()->where('height', $heightFilter)->exists();
            if (! $exists) {
                return $this->errorResponse('block_not_found', 'The requested block height could not be found.', Response::HTTP_NOT_FOUND);
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
