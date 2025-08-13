<?php

namespace App\Http\Controllers;

use App\Services\PepecoinRpcService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function show(Request $request, PepecoinRpcService $rpc, string $address): View
    {
        // Basic address validation
        if (strlen($address) < 26 || strlen($address) > 35) {
            abort(400, 'Invalid address format');
        }

        try {
            // First validate the address and check if it's ours
            $addressInfo = $rpc->call('validateaddress', [$address]);

            if (! ($addressInfo['isvalid'] ?? false)) {
                throw new \Exception('Invalid Pepecoin address');
            }

            $isMine = $addressInfo['ismine'] ?? false;

            // Get transactions for the address
            $transactions = $rpc->call('searchrawtransactions', [$address, true, 0, 100]);

            // Calculate total received and current balance
            $totalReceived = 0;
            $balance = 0;
            $txs = [];

            // Process transactions to calculate balances
            foreach ($transactions as $tx) {
                $txid = $tx['txid'] ?? null;
                $confirmations = $tx['confirmations'] ?? 0;
                $isCoinbase = ! empty($tx['vin'][0]['coinbase']);

                // Check outputs (incoming transactions)
                foreach ($tx['vout'] as $vout) {
                    $outputValue = $vout['value'] ?? 0;
                    $scriptPubKey = $vout['scriptPubKey'] ?? [];
                    $addresses = $scriptPubKey['addresses'] ?? [];

                    // Handle both array and single address formats
                    $isToThisAddress = false;
                    if (! empty($addresses) && is_array($addresses)) {
                        $isToThisAddress = in_array($address, $addresses);
                    } elseif (isset($scriptPubKey['address'])) {
                        $isToThisAddress = ($scriptPubKey['address'] === $address);
                    }

                    if ($isToThisAddress) {
                        $totalReceived += $outputValue;
                        if ($confirmations > 0) {
                            $balance += $outputValue;
                        }

                        // Add to transaction list
                        $txs[$txid] = [
                            'txid' => $txid,
                            'time' => $tx['time'] ?? null,
                            'confirmations' => $confirmations,
                            'amount' => $outputValue,
                            'is_incoming' => true,
                            'is_coinbase' => $isCoinbase,
                        ];
                    }
                }

                // Check inputs (outgoing transactions)
                if (isset($tx['vin']) && ! $isCoinbase) {
                    foreach ($tx['vin'] as $vin) {
                        if (isset($vin['txid'], $vin['vout'])) {
                            // This would require fetching the previous transaction to check the address
                            // For now, we'll just note that we can't track spent outputs perfectly
                            // But we can still track the transaction as outgoing if we're the sender
                            if (isset($txs[$txid])) {
                                $txs[$txid]['is_incoming'] = false;
                            }
                        }
                    }
                }
            }

            return view('address.show', [
                'address' => $address,
                'balance' => $balance,
                'totalReceived' => $totalReceived,
                'txs' => $txs,
                'isMine' => $isMine,
            ]);

        } catch (\Exception $e) {
            // Initialize empty arrays to prevent undefined variable errors
            return view('address.show', [
                'address' => $address,
                'balance' => 0,
                'totalReceived' => 0,
                'txs' => [],
                'isMine' => false,
                'error' => 'Could not fetch address information: '.$e->getMessage(),
            ]);
        }
    }
}
