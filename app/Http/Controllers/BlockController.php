<?php

namespace App\Http\Controllers;

use App\Services\PepecoinRpcService;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function show(PepecoinRpcService $rpc, string $hashOrHeight): View
    {
        $blockHash = $this->resolveBlockHash($rpc, $hashOrHeight);
        $block = $this->getBlockOrFail($rpc, $blockHash);
        $navigation = $this->getNavigationHashes($rpc, $block['height']);

        return view('block.show', [
            'block' => $block,
            'blockHeight' => $block['height'],
            'blockHash' => $blockHash,
            'prevBlockHash' => $navigation['prev'],
            'nextBlockHash' => $navigation['next'],
        ]);
    }

    private function resolveBlockHash(PepecoinRpcService $rpc, string $hashOrHeight): string
    {
        try {
            if (is_numeric($hashOrHeight)) {
                $height = (int) $hashOrHeight;
                if ($height < 0) {
                    abort(404, 'Block not found');
                }

                return $rpc->getBlockHash($height);
            }

            return $hashOrHeight;
        } catch (\Exception $e) {
            abort(404, 'Block not found');
        }
    }

    private function getBlockOrFail(PepecoinRpcService $rpc, string $blockHash): array
    {
        try {
            return $rpc->getBlock($blockHash, 2);
        } catch (\Exception $e) {
            abort(404, 'Block not found');
        }
    }

    private function getNavigationHashes(PepecoinRpcService $rpc, int $height): array
    {
        $prevBlockHash = $height > 0 ? $this->getBlockHashSafely($rpc, $height - 1) : null;
        $nextBlockHash = $this->getBlockHashSafely($rpc, $height + 1);

        return [
            'prev' => $prevBlockHash,
            'next' => $nextBlockHash,
        ];
    }

    private function getBlockHashSafely(PepecoinRpcService $rpc, int $height): ?string
    {
        try {
            return $rpc->getBlockHash($height);
        } catch (\Exception $e) {
            return null;
        }
    }
}
