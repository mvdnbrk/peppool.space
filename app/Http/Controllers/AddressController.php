<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\TransactionData;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Throwable;

class AddressController extends Controller
{
    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function show(Request $request, string $address): View
    {
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = in_array((int) $request->query('per_page'), $allowedPerPage, true) ? (int) $request->query('per_page') : 10;
        $page = (int) $request->query('page', 1);
        $afterTxid = $request->query('after');

        if (strlen($address) < 26 || strlen($address) > 35) {
            abort(400, 'Invalid address format');
        }

        try {
            $addressInfo = $this->blockchain->getAddress($address);
            $totalTxCount = $addressInfo->chainStats->txCount + $addressInfo->mempoolStats->txCount;

            $allTransactions = collect();
            $currentAfter = ($page > 1) ? $afterTxid : null;
            $maxCalls = 20; // Safety limit to prevent excessive API calls
            $calls = 0;

            if ($page > 1 && $afterTxid) {
                // Fast path: we have a cursor from a previous page
                while ($allTransactions->count() < $perPage && $calls < $maxCalls) {
                    $batch = $this->blockchain->getAddressTransactions($address, $currentAfter);
                    if ($batch->isEmpty()) {
                        break;
                    }
                    $allTransactions = $allTransactions->concat($batch);
                    $currentAfter = $batch->last()->txid;
                    $calls++;
                }
                $paginatedItems = $allTransactions->take($perPage);
            } else {
                // Iterative fetch to reach the requested page from the beginning
                $tempAfter = null;
                $needed = $page * $perPage;
                while ($allTransactions->count() < $needed && $calls < $maxCalls) {
                    $batch = $this->blockchain->getAddressTransactions($address, $tempAfter);
                    if ($batch->isEmpty()) {
                        break;
                    }
                    $allTransactions = $allTransactions->concat($batch);
                    $tempAfter = $batch->last()->txid;
                    $calls++;
                }
                $paginatedItems = $allTransactions->slice(($page - 1) * $perPage, $perPage);
            }

            $transactions = $paginatedItems->map(fn (TransactionData $tx) => [
                'txid' => $tx->txid,
                'time' => $tx->status->blockTime,
                'confirmations' => $tx->status->confirmed ? ($this->blockchain->getBlockTipHeight() - $tx->status->blockHeight + 1) : 0,
                'amount' => $this->calculateAddressAmount($address, $tx),
                'is_incoming' => $this->isIncoming($address, $tx),
                'is_coinbase' => (bool) ($tx->vin[0]->isCoinbase ?? false),
            ])->values();

            $paginatedTransactions = new LengthAwarePaginator(
                $transactions,
                $totalTxCount,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Append the cursor for the next page to the pagination links
            if ($paginatedItems->isNotEmpty()) {
                $paginatedTransactions->appends(['after' => $paginatedItems->last()->txid]);
            }

            return view('address.show', [
                'address' => $address,
                'balance' => $addressInfo->getTotalBalance(),
                'totalReceived' => $addressInfo->chainStats->getTotalReceived() + $addressInfo->mempoolStats->getTotalReceived(),
                'totalSent' => $addressInfo->chainStats->getTotalSent() + $addressInfo->mempoolStats->getTotalSent(),
                'txCount' => $totalTxCount,
                'transactions' => $paginatedTransactions,
            ]);

        } catch (Throwable $e) {
            return view('address.show', [
                'address' => $address,
                'balance' => null,
                'totalReceived' => null,
                'totalSent' => null,
                'txCount' => 0,
                'transactions' => new LengthAwarePaginator([], 0, $perPage),
                'error' => 'Could not fetch address data: '.$e->getMessage(),
            ]);
        }
    }

    private function calculateAddressAmount(string $address, TransactionData $tx): float
    {
        $incoming = collect($tx->vout)
            ->filter(fn ($out) => $out->scriptpubkeyAddress === $address)
            ->sum('value');

        $outgoing = collect($tx->vin)
            ->filter(fn ($in) => $in->prevout?->scriptpubkeyAddress === $address)
            ->sum(fn ($in) => $in->prevout?->value ?? 0);

        return abs($incoming - $outgoing) / 100_000_000;
    }

    private function isIncoming(string $address, TransactionData $tx): bool
    {
        $inValue = collect($tx->vout)->filter(fn ($out) => $out->scriptpubkeyAddress === $address)->sum('value');
        $outValue = collect($tx->vin)->filter(fn ($in) => $in->prevout?->scriptpubkeyAddress === $address)->sum(fn ($in) => $in->prevout?->value ?? 0);

        return $inValue > $outValue;
    }
}
