<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\BlockchainServiceInterface;
use App\Data\Blockchain\TransactionData;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AddressController extends Controller
{
    public function __construct(
        private readonly BlockchainServiceInterface $blockchain,
    ) {}

    public function show(Request $request, string $address): View
    {
        $allowedPerPage = [25, 50, 100];
        $perPage = in_array((int) $request->query('per_page'), $allowedPerPage, true) ? (int) $request->query('per_page') : 25;
        $afterTxid = $request->query('after');

        if (strlen($address) < 26 || strlen($address) > 35) {
            abort(400, 'Invalid address format');
        }

        try {
            $addressInfo = $this->blockchain->getAddress($address);
            $totalTxCount = $addressInfo->chainStats->txCount + $addressInfo->mempoolStats->txCount;

            $allTransactions = collect();
            $currentAfter = $afterTxid;
            $maxCalls = (int) ceil($perPage / 25);
            $calls = 0;

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

            $transactions = $paginatedItems->map(fn (TransactionData $tx) => [
                'txid' => $tx->txid,
                'time' => $tx->status->blockTime,
                'confirmations' => $tx->status->confirmed ? ($this->blockchain->getBlockTipHeight() - $tx->status->blockHeight + 1) : 0,
                'amount' => $this->calculateAddressAmount($address, $tx),
                'is_incoming' => $this->isIncoming($address, $tx),
                'is_coinbase' => (bool) ($tx->vin[0]->isCoinbase ?? false),
            ])->values();

            return view('address.show', [
                'address' => $address,
                'balance' => $addressInfo->getTotalBalance(),
                'totalReceived' => $addressInfo->chainStats->getTotalReceived() + $addressInfo->mempoolStats->getTotalReceived(),
                'totalSent' => $addressInfo->chainStats->getTotalSent() + $addressInfo->mempoolStats->getTotalSent(),
                'txCount' => $totalTxCount,
                'transactions' => $transactions,
                'nextAfter' => $paginatedItems->count() >= $perPage
                    ? $paginatedItems->last(fn (TransactionData $tx) => $tx->status->confirmed)?->txid
                    : null,
                'perPage' => $perPage,
                'after' => $afterTxid,
            ]);

        } catch (RequestException $e) {
            abort(in_array($e->response->status(), [Response::HTTP_BAD_REQUEST, Response::HTTP_NOT_FOUND])
                ? Response::HTTP_NOT_FOUND
                : Response::HTTP_SERVICE_UNAVAILABLE);
        } catch (Throwable) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'Address data is temporarily unavailable.');
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
