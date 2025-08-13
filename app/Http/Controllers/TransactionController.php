<?php

namespace App\Http\Controllers;

use App\Services\PepecoinRpcService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function show(Request $request, PepecoinRpcService $rpc, string $txid): View
    {
        try {
            // Get transaction details
            $transaction = $rpc->call('getrawtransaction', [$txid, true]);

            // Check if transaction is in a block or mempool
            $inBlock = isset($transaction['blockhash']);
            $blockInfo = null;

            if ($inBlock) {
                $blockHash = $transaction['blockhash'];
                $block = $rpc->getBlock($blockHash, 1);
                $blockInfo = [
                    'hash' => $blockHash,
                    'height' => $block['height'],
                    'time' => $block['time'],
                    'confirmations' => $transaction['confirmations'] ?? 0,
                ];
            }

            // Calculate total input and output values
            $totalInput = 0;
            $totalOutput = 0;
            $fee = 0;

            // Calculate outputs
            foreach ($transaction['vout'] as $output) {
                $totalOutput += $output['value'];
            }

            // Process inputs (skip coinbase transactions)
            $inputs = [];
            if (! isset($transaction['vin'][0]['coinbase'])) {
                foreach ($transaction['vin'] as $input) {
                    $inputData = [
                        'txid' => $input['txid'] ?? null,
                        'vout' => $input['vout'] ?? null,
                        'address' => null,
                        'value' => 0,
                        'scriptSig' => $input['scriptSig']['hex'] ?? null,
                    ];

                    if (isset($input['txid']) && isset($input['vout'])) {
                        try {
                            $prevTx = $rpc->call('getrawtransaction', [$input['txid'], true]);
                            if (isset($prevTx['vout'][$input['vout']])) {
                                $prevOut = $prevTx['vout'][$input['vout']];
                                $inputData['value'] = $prevOut['value'];
                                $totalInput += $prevOut['value'];

                                // Extract address from previous output
                                if (isset($prevOut['scriptPubKey']['addresses'][0])) {
                                    $inputData['address'] = $prevOut['scriptPubKey']['addresses'][0];
                                } elseif (isset($prevOut['scriptPubKey']['address'])) {
                                    $inputData['address'] = $prevOut['scriptPubKey']['address'];
                                }
                            }
                            $inputData['prevTx'] = $prevTx; // Keep full previous tx for reference
                        } catch (\Exception $e) {
                            // Previous transaction not found, skip
                            \Log::warning("Could not fetch previous transaction {$input['txid']}: ".$e->getMessage());
                        }
                    }
                    $inputs[] = $inputData; // Add the input data to the inputs array
                }
                $fee = $totalInput - $totalOutput;
            }

            // Update transaction with enriched input data
            $transaction['vin'] = $inputs;

            return view('transaction.show', [
                'transaction' => $transaction,
                'txid' => $txid,
                'inBlock' => $inBlock,
                'blockInfo' => $blockInfo,
                'totalInput' => $totalInput,
                'totalOutput' => $totalOutput,
                'fee' => $fee,
                'isCoinbase' => isset($transaction['vin'][0]['coinbase']),
            ]);

        } catch (\Exception $e) {
            return view('transaction.show', [
                'error' => 'Transaction not found: '.$e->getMessage(),
                'txid' => $txid,
            ]);
        }
    }
}
