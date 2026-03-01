<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\BlockchainServiceInterface;
use Illuminate\View\View;
use Throwable;

class BlockController extends Controller
{
    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function show(string $hashOrHeight): View
    {
        try {
            $blockHash = $this->resolveBlockHash($hashOrHeight);
            $block = $this->blockchain->getBlock($blockHash);
            $transactions = $this->blockchain->getBlockTransactions($blockHash);

            return view('block.show', [
                'block' => [
                    'hash' => $block->id,
                    'height' => $block->height,
                    'time' => $block->timestamp,
                    'difficulty' => $block->difficulty,
                    'size' => $block->size,
                    'tx' => $transactions,
                    'previousblockhash' => $block->previousblockhash,
                ],
                'blockHeight' => $block->height,
                'blockHash' => $blockHash,
                'prevBlockHash' => $block->height > 0 ? true : null,
                'nextBlockHash' => $block->height < $this->blockchain->getBlockTipHeight() ? true : null,
            ]);
        } catch (Throwable) {
            abort(404, 'Block not found');
        }
    }

    private function resolveBlockHash(string $hashOrHeight): string
    {
        if (is_numeric($hashOrHeight)) {
            return $this->blockchain->getBlockHash((int) $hashOrHeight);
        }

        return $hashOrHeight;
    }
}
