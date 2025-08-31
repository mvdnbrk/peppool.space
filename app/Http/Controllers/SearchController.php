<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Services\PepecoinRpcService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function store(Request $request, PepecoinRpcService $rpc)
    {
        $q = trim((string) ($request->input('q') ?? $request->query('q') ?? ''));

        if ($q === '') {
            return redirect()->route('search.index')->with('error', 'Please enter a search term.');
        }

        // Address: Pepecoin base58 addresses start with 'P' (length 26-34 typically)
        if (preg_match('/^P[1-9A-HJ-NP-Za-km-z]{25,33}$/', $q)) {
            return redirect()->route('address.show', ['address' => $q]);
        }

        // Numeric height (allow separators like spaces, commas, dots)
        $qDigits = preg_replace('/[\s,\.]/', '', $q);
        if ($qDigits !== '' && ctype_digit($qDigits)) {
            $height = (int) $qDigits;

            // Validate against best known height (DB first, then RPC fallback)
            $best = Block::max('height');
            if ($best === null) {
                try {
                    $best = $rpc->getBlockCount();
                } catch (\Throwable $e) {
                    $best = null;
                }
            }

            if ($best !== null && $height > (int) $best) {
                return redirect()->route('search.index')->with('error', 'Block height out of range.');
            }

            return redirect()->route('block.show', ['hashOrHeight' => $height]);
        }

        // 64-hex: could be block hash or txid.
        if (preg_match('/^[0-9a-fA-F]{64}$/', $q)) {
            // Try DB first (faster): is this a known block hash?
            if (Block::where('hash', $q)->exists()) {
                return redirect()->route('block.show', ['hashOrHeight' => $q]);
            }

            // Fallback to RPC: check block hash, then mempool tx, then raw transaction
            try {
                $rpc->getBlock($q, 1);

                return redirect()->route('block.show', ['hashOrHeight' => $q]);
            } catch (\Throwable $e) {
                // Check mempool first (works without txindex)
                try {
                    $rpc->getMempoolEntry($q);

                    return redirect()->route('transaction.show', ['txid' => strtolower($q)]);
                } catch (\Throwable $eMem) {
                    // Try verbose raw transaction (requires txindex or blockhash but may work on some nodes)
                    try {
                        $rpc->getRawTransaction($q, true);

                        return redirect()->route('transaction.show', ['txid' => strtolower($q)]);
                    } catch (\Throwable $eRaw) {
                        // fall through
                    }
                }
            }
        }

        return redirect()->back()->with('error', 'No matching block, transaction, or address found.');
    }
}
