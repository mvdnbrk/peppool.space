<?php

namespace App\Http\Controllers;

use App\Services\PepecoinRpcService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function show(Request $request, PepecoinRpcService $rpc, string $hashOrHeight): View
    {
        try {
            // Determine if input is hash or height
            $blockHash = null;
            $blockHeight = null;

            if (is_numeric($hashOrHeight)) {
                // It's a block height
                $blockHeight = (int) $hashOrHeight;
                $blockHash = $rpc->getBlockHash($blockHeight);
            } else {
                // It's a block hash
                $blockHash = $hashOrHeight;
                $block = $rpc->getBlock($blockHash, 1);
                $blockHeight = $block['height'];
            }

            // Get detailed block information
            $block = $rpc->getBlock($blockHash, 2); // Verbosity 2 for transaction details

            // Get previous and next block hashes for navigation
            $prevBlockHash = null;
            $nextBlockHash = null;

            if ($blockHeight > 0) {
                $prevBlockHash = $rpc->getBlockHash($blockHeight - 1);
            }

            try {
                $nextBlockHash = $rpc->getBlockHash($blockHeight + 1);
            } catch (\Exception $e) {
                // Next block doesn't exist yet
                $nextBlockHash = null;
            }

            return view('block.show', [
                'block' => $block,
                'blockHeight' => $blockHeight,
                'blockHash' => $blockHash,
                'prevBlockHash' => $prevBlockHash,
                'nextBlockHash' => $nextBlockHash,
            ]);

        } catch (\Exception $e) {
            return view('block.show', [
                'error' => 'Block not found: '.$e->getMessage(),
                'blockHash' => $hashOrHeight,
            ]);
        }
    }
}
