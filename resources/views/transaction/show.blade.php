<x-layout
    title="Transaction {{ $txid ?? 'Not Found' }} - peppool.space"
>
        @if(isset($error))
            <!-- Error State -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ $error }}</h3>
                    </div>
                </div>
            </div>
        @else
            <!-- Transaction Header -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">Transaction Details</h1>
                    <div class="flex items-center space-x-2">
                        @if($isCoinbase)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Coinbase
                            </span>
                        @endif
                        @if($inBlock)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Confirmed
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Unconfirmed
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Transaction ID</h3>
                    <p class="text-sm font-mono bg-gray-100 p-3 rounded break-all">{{ $txid }}</p>
                </div>

                @if($inBlock && $blockInfo)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Block Hash</h3>
                            <p class="text-sm font-mono bg-gray-100 p-2 rounded break-all">
                                <a href="{{ route('block.show', $blockInfo['hash']) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $blockInfo['hash'] }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Block Height</h3>
                            <p class="text-sm bg-gray-100 p-2 rounded">
                                <a href="{{ route('block.show', $blockInfo['height']) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ number_format($blockInfo['height']) }}
                                </a>
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Transaction Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                @if($inBlock)
                                    Block Time
                                @else
                                    Received Time
                                @endif
                            </p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                @if($inBlock && $blockInfo)
                                    {{ date('Y-m-d H:i:s', $blockInfo['time']) }}
                                @else
                                    {{ date('Y-m-d H:i:s', $transaction['time'] ?? time()) }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($inBlock && $blockInfo)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmations</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($blockInfo['confirmations']) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($transaction['size'] ?? 0) }} bytes</p>
                        </div>
                    </div>
                </div>

                @if(!$isCoinbase && $fee > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee</p>
                                @php
                                    $formattedFee = rtrim(rtrim(number_format($fee, 8, '.', ','), '0'), '.');
                                    $parts = explode('.', $formattedFee);
                                @endphp
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $parts[0] }}<span class="text-sm">@if(isset($parts[1])).<span class="text-xs">{{ $parts[1] }}</span>@endif</span> <span>PEPE</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Transaction Flow -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Inputs -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ count($transaction['vin'] ?? []) }} {{ Str::plural('Input', count($transaction['vin'] ?? [])) }}
                            @if(!$isCoinbase && $totalInput > 0)
                                @php
                                    $formattedInput = rtrim(rtrim(number_format($totalInput, 8, '.', ','), '0'), '.');
                                    $parts = explode('.', $formattedInput);
                                @endphp
                                <span class="text-sm font-normal text-gray-500">- {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($transaction['vin'] ?? [] as $input)
                            <div class="px-6 py-4">
                                @if(isset($input['coinbase']))
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Coinbase
                                        </span>
                                        <span class="text-sm text-gray-600">Block Reward</span>
                                    </div>
                                    <div class="mt-2 text-xs font-mono text-gray-500 break-all">
                                        {{ $input['coinbase'] }}
                                    </div>
                                @else
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">
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
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Input #{{ $loop->iteration }}
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500 mt-1">
                                                    No input (Newly generated coins)
                                                </div>
                                            @endif
                                            @if(isset($input['address']))
                                                <div class="mt-2 text-xs font-mono bg-gray-100 p-2 rounded break-all">
                                                    {{ $input['address'] }}
                                                </div>
                                            @elseif(isset($input['scriptPubKey']) && isset($input['scriptPubKey']['addresses']))
                                                @foreach($input['scriptPubKey']['addresses'] as $address)
                                                    <div class="mt-2 text-xs font-mono bg-gray-100 p-2 rounded break-all">
                                                        {{ $address }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500">
                                No inputs
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Outputs -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ count($transaction['vout'] ?? []) }} {{ Str::plural('Output', count($transaction['vout'] ?? [])) }}
                            @if($totalOutput > 0)
                                @php
                                    $formattedOutput = rtrim(rtrim(number_format($totalOutput, 8, '.', ','), '0'), '.');
                                    $parts = explode('.', $formattedOutput);
                                @endphp
                                <span class="text-sm font-normal text-gray-500">- {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span></span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse($transaction['vout'] ?? [] as $output)
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            @php
                                                $formattedValue = rtrim(rtrim(number_format($output['value'], 8, '.', ','), '0'), '.');
                                                $parts = explode('.', $formattedValue);
                                            @endphp
                                            {{ $parts[0] }}@if(isset($parts[1]))<span class="text-xs">.{{ $parts[1] }}</span>@endif <span>PEPE</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Output #{{ $output['n'] }}
                                        </div>
                                        @if(isset($output['scriptPubKey']['addresses']))
                                            <div class="mt-2">
                                                @foreach($output['scriptPubKey']['addresses'] as $address)
                                                    <div class="text-xs font-mono bg-gray-100 p-2 rounded break-all">
                                                        {{ $address }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif(isset($output['scriptPubKey']['hex']))
                                            <div class="mt-2 text-xs font-mono text-gray-500 break-all">
                                                Script: {{ $output['scriptPubKey']['hex'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center text-gray-500">
                                No outputs
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
</x-layout>
