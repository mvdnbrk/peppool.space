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
            <!-- Transaction Header -->
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaction Details</h1>
                    <div class="flex items-center space-x-2">
                        @if($isCoinbase)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                Coinbase
                            </span>
                        @endif
                        @if($inBlock)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                Confirmed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                Unconfirmed
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Transaction ID</h3>
                    <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-3 rounded break-all">{{ $txid }}</p>
                </div>

                @if($inBlock && $blockInfo)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Block Hash</h3>
                            <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                <a href="{{ route('block.show', $blockInfo['hash']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $blockInfo['hash'] }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Block Height</h3>
                            <p class="text-sm bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded">
                                <a href="{{ route('block.show', $blockInfo['height']) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ number_format($blockInfo['height']) }}
                                </a>
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Transaction Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-stat-card icon-bg="bg-blue-500" label="{{ $inBlock ? 'Time' : 'Received' }}">
                    <x-slot:icon>
                        <x-icon-clock class="w-5 h-5 text-white" />
                    </x-slot:icon>
                    @php
                        $ts = $inBlock && $blockInfo
                            ? \Carbon\Carbon::createFromTimestamp($blockInfo['time'])
                            : \Carbon\Carbon::createFromTimestamp($transaction['timereceived'] ?? $transaction['time'] ?? time());
                    @endphp
                    <timestamp
                        x-data="timestamp"
                        datetime="{{ $ts->toAtomString() }}"
                        x-text="relativeTime"
                        title="{{ $ts->format('Y-m-d H:i:s') }}"
                        class="text-lg font-bold text-gray-900 dark:text-white"></timestamp>
                </x-stat-card>

                @if($inBlock && $blockInfo)
                    <x-stat-card icon-bg="bg-green-500" label="Confirmations">
                        <x-slot:icon>
                            <x-icon-check-circle class="w-5 h-5 text-white" />
                        </x-slot:icon>
                        <span class="text-lg">{{ number_format($blockInfo['confirmations']) }}</span>
                    </x-stat-card>
                @endif

                <x-stat-card icon-bg="bg-orange-500" label="Size">
                    <x-slot:icon>
                        <x-icon-database class="w-5 h-5 text-white" />
                    </x-slot:icon>
                    <span class="text-lg">{{ number_format($transaction['size'] ?? 0) }} bytes</span>
                </x-stat-card>

                @if(!$isCoinbase && $fee > 0)
                    <x-stat-card icon-bg="bg-blue-500/10" label="Fee">
                        <x-slot:icon>
                            <x-icon-currency-dollar class="w-5 h-5 text-blue-600" />
                        </x-slot:icon>
                        @php
                            $formattedFee = rtrim(rtrim(number_format($fee, 8, '.', ','), '0'), '.');
                            $parts = explode('.', $formattedFee);
                        @endphp
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $parts[0] }}<span class="text-sm">@if(isset($parts[1])).<span class="text-xs">{{ $parts[1] }}</span>@endif</span> <span>PEPE</span>
                        </span>
                    </x-stat-card>
                @endif
            </div>

            <!-- Transaction Flow -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Inputs -->
                <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ count($transaction['vin'] ?? []) }} {{ Str::plural('Input', count($transaction['vin'] ?? [])) }}
                            @if(!$isCoinbase && $totalInput > 0)
                                @php
                                    $formattedInput = rtrim(rtrim(number_format($totalInput, 8, '.', ','), '0'), '.');
                                    $parts = explode('.', $formattedInput);
                                @endphp
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">- {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span></span>
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
                                                    @php
                                                        $formattedValue = rtrim(rtrim(number_format($input['value'], 8, '.', ','), '0'), '.');
                                                        $parts = explode('.', $formattedValue);
                                                    @endphp
                                                    {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span>
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
                                                    {{ $input['address'] }}
                                                </div>
                                            @elseif(isset($input['scriptPubKey']) && isset($input['scriptPubKey']['addresses']))
                                                @foreach($input['scriptPubKey']['addresses'] as $address)
                                                    <div class="mt-2 text-xs font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                                        {{ $address }}
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
                                @php
                                    $formattedOutput = rtrim(rtrim(number_format($totalOutput, 8, '.', ','), '0'), '.');
                                    $parts = explode('.', $formattedOutput);
                                @endphp
                                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">- {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transaction['vout'] ?? [] as $output)
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $formattedValue = rtrim(rtrim(number_format($output['value'], 8, '.', ','), '0'), '.');
                                                $parts = explode('.', $formattedValue);
                                            @endphp
                                            {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Output #{{ $output['n'] }}
                                        </div>
                                        @if(isset($output['scriptPubKey']['addresses']))
                                            <div class="mt-2">
                                                @foreach($output['scriptPubKey']['addresses'] as $address)
                                                    <div class="text-xs font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
                                                        {{ $address }}
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
