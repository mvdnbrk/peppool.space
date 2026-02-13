<?php

namespace App\Http\Controllers;

use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function __construct(
        private readonly ElectrsPepeService $electrs,
        private readonly PepecoinExplorerService $explorer,
    ) {}

    public function show(string $hashOrHeight): View
    {
        try {
            $blockHash = $this->resolveBlockHash($hashOrHeight);
            $block = $this->electrs->getBlock($blockHash);
            $transactions = $this->electrs->getBlockTransactions($blockHash);

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
                'nextBlockHash' => $block->height < $this->explorer->getBlockTipHeight() ? true : null,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Block not found');
        }
    }

    private function resolveBlockHash(string $hashOrHeight): string
    {
        if (is_numeric($hashOrHeight)) {
            return $this->electrs->getBlockHash((int) $hashOrHeight);
        }

        return $hashOrHeight;
    }
}
