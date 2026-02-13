<?php

namespace App\Http\Controllers;

use App\Services\ElectrsPepeService;
use App\Services\PepecoinExplorerService;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(
        private readonly ElectrsPepeService $electrs,
        private readonly PepecoinExplorerService $explorer,
    ) {}

    public function show(string $txid): View
    {
        try {
            $tx = $this->electrs->getTransaction($txid);

            $inBlock = $tx->status->confirmed;
            $blockInfo = null;

            if ($inBlock) {
                $blockInfo = [
                    'hash' => $tx->status->blockHash,
                    'height' => $tx->status->blockHeight,
                    'time' => $tx->status->blockTime,
                    'confirmations' => $this->explorer->getBlockTipHeight() - $tx->status->blockHeight + 1,
                ];
            }

            $inputs = collect($tx->vin)->map(fn ($in, $index) => [
                'txid' => $in->txid,
                'vout' => $in->vout,
                'address' => $in->prevout?->scriptpubkeyAddress,
                'value' => $in->getValueInPep(),
                'coinbase' => $in->isCoinbase ? ($in->scriptsig ?? 'Coinbase Transaction') : null,
            ])->toArray();

            $outputs = collect($tx->vout)->map(fn ($out, $index) => [
                'n' => $index,
                'value' => $out->getValueInPep(),
                'scriptPubKey' => [
                    'addresses' => isset($out->scriptpubkeyAddress) ? [$out->scriptpubkeyAddress] : null,
                    'hex' => $out->scriptpubkey,
                    'type' => $out->scriptpubkeyType,
                ],
            ])->toArray();

            $transaction = [
                'txid' => $tx->txid,
                'size' => $tx->size,
                'version' => $tx->version,
                'locktime' => $tx->locktime,
                'vin' => $inputs,
                'vout' => $outputs,
                'time' => $tx->status->blockTime,
            ];

            return view('transaction.show', [
                'transaction' => $transaction,
                'txid' => $txid,
                'inBlock' => $inBlock,
                'blockInfo' => $blockInfo,
                'totalInput' => $tx->getTotalInputValueInPep(),
                'totalOutput' => $tx->getTotalOutputValueInPep(),
                'fee' => $tx->getFeeInPep(),
                'isCoinbase' => (bool) ($tx->vin[0]->isCoinbase ?? false),
            ]);

        } catch (\Exception $e) {
            return view('transaction.show', [
                'error' => 'Transaction not found: '.$e->getMessage(),
                'txid' => $txid,
            ]);
        }
    }
}
