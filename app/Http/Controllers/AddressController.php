<?php

namespace App\Http\Controllers;

use App\Models\AddressBalance;
use App\Models\Transaction;
use App\Models\TransactionOutput;
use App\Services\PepecoinExplorerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorer,
    ) {}

    public function show(Request $request, string $address): View
    {
        // Per-page selector with allowed values
        $allowedPerPage = [10, 25, 50, 100];
        $requestedPerPage = (int) $request->query('per_page', 25);
        $perPageGlobal = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 25;
        // Basic address validation
        if (strlen($address) < 26 || strlen($address) > 35) {
            abort(400, 'Invalid address format');
        }

        try {
            // First try to get address data from database
            $addressBalance = AddressBalance::where('address', $address)->first();

            if ($addressBalance) {
                // Address found in database - get combined (incoming + outgoing) transaction history (paginated)
                $transactions = $this->getAddressTransactionsCombined($address, $perPageGlobal);

                return view('address.show', [
                    'address' => $address,
                    'balance' => $addressBalance->balance,
                    'totalReceived' => $addressBalance->total_received,
                    'totalSent' => $addressBalance->total_sent,
                    'txCount' => $addressBalance->tx_count,
                    'transactions' => $transactions,
                    'isMine' => false, // Database addresses are not wallet addresses
                ]);
            }

            $addressInfo = $this->explorer->validateAddress($address);

            if (! $addressInfo->isValid) {
                throw new \Exception('Invalid Pepecoin address');
            }

            $isMine = (bool) $addressInfo->isMine;

            if ($isMine) {
                // For wallet addresses, get balance using listunspent
                try {
                    $unspent = $this->explorer->listUnspentData($address, 0, 9_999_999);
                    $balance = array_sum(array_map(static fn ($u) => $u->amount, $unspent));

                    // Build transaction list from unspent outputs
                    $txs = [];
                    foreach ($unspent as $utxo) {
                        $txs[$utxo->txid] = [
                            'txid' => $utxo->txid,
                            'time' => null,
                            'confirmations' => $utxo->confirmations,
                            'amount' => $utxo->amount,
                            'is_incoming' => true,
                            'is_coinbase' => false,
                        ];
                    }
                    // Enrich with mined timestamps if present in DB
                    if (! empty($txs)) {
                        $txids = array_keys($txs);
                        $times = Transaction::query()
                            ->select(['transactions.tx_id', 'blocks.created_at as mined_at'])
                            ->join('blocks', 'transactions.block_height', '=', 'blocks.height')
                            ->whereIn('transactions.tx_id', $txids)
                            ->get()
                            ->keyBy('tx_id');
                        foreach ($times as $txid => $row) {
                            if (isset($txs[$txid])) {
                                $txs[$txid]['time'] = Carbon::parse($row->mined_at)->timestamp;
                            }
                        }
                    }
                    // Prefer DB-backed combined history if available; fallback to wallet-only view otherwise
                    $combined = $this->getAddressTransactionsCombined($address, $perPageGlobal);
                    if ($combined->total() > 0) {
                        return view('address.show', [
                            'address' => $address,
                            'balance' => $balance,
                            'totalReceived' => null,
                            'totalSent' => null,
                            'txCount' => $combined->total(),
                            'transactions' => $combined,
                            'isMine' => $isMine,
                        ]);
                    }

                    // Fallback: paginate wallet UTXO snapshot
                    $page = request('page', 1);
                    $perPage = $perPageGlobal;
                    $items = array_values($txs);
                    $total = count($items);
                    $slice = array_slice($items, ($page - 1) * $perPage, $perPage);
                    $paginator = new LengthAwarePaginator($slice, $total, $perPage, $page, [
                        'path' => request()->url(),
                        'query' => request()->query(),
                    ]);

                    return view('address.show', [
                        'address' => $address,
                        'balance' => $balance,
                        'totalReceived' => null,
                        'totalSent' => null,
                        'txCount' => $total,
                        'transactions' => $paginator,
                        'isMine' => $isMine,
                    ]);
                } catch (\Exception $e) {
                    // listunspent failed, show basic info
                    return view('address.show', [
                        'address' => $address,
                        'balance' => null,
                        'totalReceived' => null,
                        'totalSent' => null,
                        'txCount' => 0,
                        'transactions' => new LengthAwarePaginator([], 0, $perPageGlobal),
                        'isMine' => $isMine,
                    ]);
                }
            } else {
                // Valid address but not in database and not ours - show coming soon
                return view('address.show', [
                    'address' => $address,
                    'balance' => null,
                    'totalReceived' => null,
                    'totalSent' => null,
                    'txCount' => 0,
                    'transactions' => new LengthAwarePaginator([], 0, $perPageGlobal),
                    'isMine' => false,
                    'showComingSoon' => true,
                ]);
            }

        } catch (\Exception $e) {
            return view('address.show', [
                'address' => $address,
                'balance' => null,
                'totalReceived' => null,
                'totalSent' => null,
                'txCount' => 0,
                'transactions' => new LengthAwarePaginator([], 0, $perPageGlobal),
                'isMine' => false,
                'error' => 'Could not validate address: '.$e->getMessage(),
            ]);
        }
    }

    private function getAddressTransactionsCombined(string $address, int $perPage = 25): LengthAwarePaginator
    {
        try {
            // Respect method parameter per-page which has already been validated

            // Incoming: outputs to this address
            $incoming = TransactionOutput::query()
                ->where('transaction_outputs.address', $address)
                ->join('transactions', 'transaction_outputs.tx_id', '=', 'transactions.tx_id')
                ->join('blocks', 'transactions.block_height', '=', 'blocks.height')
                ->get([
                    'transaction_outputs.tx_id as tx_id',
                    'transaction_outputs.amount as amount',
                    'transactions.is_coinbase as is_coinbase',
                    'blocks.created_at as mined_at',
                ])
                ->map(function ($row) {
                    return [
                        'txid' => $row->tx_id,
                        'time' => Carbon::parse($row->mined_at)->timestamp,
                        'timereceived' => null,
                        'confirmations' => 0,
                        'amount' => (float) $row->amount,
                        'is_incoming' => true,
                        'is_coinbase' => (bool) $row->is_coinbase,
                    ];
                });

            // Outgoing: inputs spent from this address
            $outgoing = \App\Models\TransactionInput::query()
                ->where('transaction_inputs.address', $address)
                ->join('transactions', 'transaction_inputs.tx_id', '=', 'transactions.tx_id')
                ->join('blocks', 'transactions.block_height', '=', 'blocks.height')
                ->get([
                    'transaction_inputs.tx_id as tx_id',
                    'transaction_inputs.amount as amount',
                    'transactions.is_coinbase as is_coinbase',
                    'blocks.created_at as mined_at',
                ])
                ->map(function ($row) {
                    return [
                        'txid' => $row->tx_id,
                        'time' => Carbon::parse($row->mined_at)->timestamp,
                        'timereceived' => null,
                        'confirmations' => 0,
                        'amount' => (float) $row->amount,
                        'is_incoming' => false,
                        'is_coinbase' => (bool) $row->is_coinbase,
                    ];
                });

            $collection = $incoming->merge($outgoing)->sortByDesc('time')->values();

            $page = request('page', 1);
            $total = $collection->count();
            $items = $collection->forPage($page, $perPage)->values();

            return new LengthAwarePaginator($items, $total, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

        } catch (\Exception $e) {
            // Return empty paginator on any error to prevent 500
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }
}
