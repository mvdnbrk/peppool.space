<?php

namespace App\Http\Controllers;

use App\Data\Electrs\TransactionData;
use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorer,
        private readonly ElectrsPepeService $electrs,
    ) {}

    public function show(Request $request, string $address): View
    {
        $allowedPerPage = [10, 25, 50, 100];
        $perPage = in_array((int) $request->query('per_page'), $allowedPerPage, true) ? (int) $request->query('per_page') : 10;

        if (strlen($address) < 26 || strlen($address) > 35) {
            abort(400, 'Invalid address format');
        }

        try {
            $addressInfo = $this->electrs->getAddress($address);
            $history = $this->electrs->getAddressTransactions($address);

            $transactions = $history->map(fn (TransactionData $tx) => [
                'txid' => $tx->txid,
                'time' => $tx->status->blockTime,
                'confirmations' => $tx->status->confirmed ? ($this->explorer->getBlockTipHeight() - $tx->status->blockHeight + 1) : 0,
                'amount' => $this->calculateAddressAmount($address, $tx),
                'is_incoming' => $this->isIncoming($address, $tx),
                'is_coinbase' => (bool) ($tx->vin[0]->isCoinbase ?? false),
            ]);

            $page = request('page', 1);
            $paginatedTransactions = new LengthAwarePaginator(
                $transactions->forPage($page, $perPage),
                $transactions->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('address.show', [
                'address' => $address,
                'balance' => $addressInfo->getTotalBalance(),
                'totalReceived' => $addressInfo->chainStats->getTotalReceived() + $addressInfo->mempoolStats->getTotalReceived(),
                'totalSent' => $addressInfo->chainStats->getTotalSent() + $addressInfo->mempoolStats->getTotalSent(),
                'txCount' => $addressInfo->chainStats->txCount + $addressInfo->mempoolStats->txCount,
                'transactions' => $paginatedTransactions,
            ]);

        } catch (\Exception $e) {
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
