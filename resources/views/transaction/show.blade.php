<x-layout
    title="Transaction {{ $txid ?? 'Not Found' }} - peppool.space"
    :og_description="(isset($txid) ? 'Details for Pepecoin transaction ' . $txid : 'Transaction not found') . ' on peppool.space: confirmations, inputs, outputs, size and block info.'"
    og_image="pepecoin-transaction.png"
>
        @if(isset($error))
            <!-- Error State -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon-exclamation-circle class="w-5 h-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ $error }}</h3>
                    </div>
                </div>
            </div>
        @else
            @php
            $txProps = [
                'txid' => $txid,
                'isCoinbase' => $isCoinbase,
                'initialConfirmed' => $inBlock,
                'initialBlockHash' => $blockInfo['hash'] ?? '',
                'initialBlockHeight' => $blockInfo['height'] ?? '',
                'initialBlockTime' => $transaction['time'] ?? '',
                'initialConfirmations' => $blockInfo['confirmations'] ?? 0,
                'initialBlockTipHeight' => $blockTipHeight,
                'size' => $transaction['size'] ?? 0,
                'fee' => $fee,
                'statusApiUrl' => route('api.tx.status', $txid),
                'tipHeightApiUrl' => route('api.blocks.tip.height')
            ];
            @endphp
            <div 
                data-vue="transaction-details"
                data-props='@json($txProps)'
            ></div>

            <!-- Transaction Flow -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Inputs -->
                <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ count($transaction['vin'] ?? []) }} {{ Str::plural('Input', count($transaction['vin'] ?? [])) }}
                            @if(!$isCoinbase && $totalInput > 0)
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">- <x-pepe-amount :amount="$totalInput" /> <span>PEPE</span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transaction['vin'] ?? [] as $input)
                            <div class="px-6 py-4">
                                @if(isset($input['coinbase']))
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                            Coinbase
                                        </span>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Block Reward</span>
                                    </div>
                                    <div class="mt-2 text-xs font-mono text-gray-500 dark:text-gray-400 break-all">
                                        {{ $input['coinbase'] }}
                                    </div>
                                @else
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                @if(isset($input['value']))
                                                    <x-pepe-amount :amount="$input['value']" /> <span>PEPE</span>
                                                @else
                                                    Input
                                                @endif
                                            </div>
                                            @if(isset($input['txid']))
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    Input #{{ $loop->iteration }}
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    No input (Newly generated coins)
                                                </div>
                                            @endif
                                            @if(isset($input['address']))
                                                <div class="mt-2 text-xs font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                                    <a href="{{ route('address.show', $input['address']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                        {{ $input['address'] }}
                                                    </a>
                                                </div>
                                            @elseif(isset($input['scriptPubKey']) && isset($input['scriptPubKey']['addresses']))
                                                @foreach($input['scriptPubKey']['addresses'] as $address)
                                                    <div class="mt-2 text-xs font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                                        <a href="{{ route('address.show', $address) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                            {{ $address }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No inputs
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Outputs -->
                <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ count($transaction['vout'] ?? []) }} {{ Str::plural('Output', count($transaction['vout'] ?? [])) }}
                            @if($totalOutput > 0)
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">- <x-pepe-amount :amount="$totalOutput" /> <span>PEPE</span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transaction['vout'] ?? [] as $output)
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <x-pepe-amount :amount="$output['value']" /> <span>PEPE</span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Output #{{ $output['n'] }}
                                        </div>
                                        @if(isset($output['scriptPubKey']['addresses']))
                                            <div class="mt-2">
                                                @foreach($output['scriptPubKey']['addresses'] as $address)
                                                    <div class="text-xs font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                                        <a href="{{ route('address.show', $address) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                            {{ $address }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif(isset($output['scriptPubKey']['hex']))
                                            <div class="mt-2 text-xs font-mono text-gray-500 dark:text-gray-400 break-all">
                                                Script: {{ $output['scriptPubKey']['hex'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No outputs
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
</x-layout>
