<?php

namespace App\Http\Controllers;

use App\Services\PepecoinRpcService;
use Illuminate\View\View;

class BlockController extends Controller
{
    public function show(PepecoinRpcService $rpc, string $hashOrHeight): View
    {
        // Convert height to hash if needed
        $blockHash = is_numeric($hashOrHeight)
            ? $rpc->getBlockHash((int) $hashOrHeight)
            : $hashOrHeight;

        // Get block details with error handling
        try {
            $block = $rpc->getBlock($blockHash, 2);
        } catch (\Exception $e) {
            abort(404, 'Block not found');
        }

        // Get navigation hashes
        $prevBlockHash = $block['height'] > 0
            ? $rpc->getBlockHash($block['height'] - 1)
            : null;

        // Get next block hash if it exists
        $nextBlockHash = null;
        try {
            $nextBlockHash = $rpc->getBlockHash($block['height'] + 1);
        } catch (\Exception $e) {
            // Next block doesn't exist yet
        }

        return view('block.show', [
            'block' => $block,
            'blockHeight' => $block['height'],
            'blockHash' => $blockHash,
            'prevBlockHash' => $prevBlockHash,
            'nextBlockHash' => $nextBlockHash,
        ]);
    }
}
