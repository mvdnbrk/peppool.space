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

            // Get balance using listunspent if this is our address
            $balance = 0;
            $totalReceived = null;
            $txs = [];

            if ($isMine) {
                try {
                    // Get unspent outputs for this address to calculate balance
                    $unspent = $rpc->call('listunspent', [0, 9999999, [$address]]);
                    $balance = array_sum(array_column($unspent, 'amount'));

                    // For owned addresses, we can get some transaction info from listunspent
                    foreach ($unspent as $utxo) {
                        $txs[$utxo['txid']] = [
                            'txid' => $utxo['txid'],
                            'time' => null, // Would need to fetch block time
                            'confirmations' => $utxo['confirmations'],
                            'amount' => $utxo['amount'],
                            'is_incoming' => true,
                            'is_coinbase' => false,
                        ];
                    }
                } catch (\Exception $e) {
                    // listunspent failed, fall back to basic info
                }
            } else {
                // For non-owned addresses, we can't get transaction history without searchrawtransactions
                return view('address.show', [
                    'address' => $address,
                    'balance' => null,
                    'totalReceived' => null,
                    'txs' => [],
                    'isMine' => $isMine,
                    'showComingSoon' => true,
                ]);
            }

            // Balance and transactions are already calculated above

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
                'balance' => null,
                'totalReceived' => null,
                'txs' => [],
                'isMine' => false,
                'error' => 'Could not validate address: '.$e->getMessage(),
            ]);
        }
    }
}
