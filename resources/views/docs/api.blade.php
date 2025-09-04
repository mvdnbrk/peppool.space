<x-layout title="Pepecoin API Documentation" og_image="pepecoin-api.png" og_description="Pepecoin API documentation: endpoints for blocks, mempool, prices and more. Includes rate limits, examples and response formats.">
        <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-400">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-gray-300 mb-3 md:mb-4">Pepecoin API Documentation</h1>
            <p class="text-sm md:text-base">
                The Peppool.space API provides programmatic access to Pepecoin blockchain data and market information.
            </p>
            <p class="text-sm md:text-base">
                All endpoints are rate-limited and return data in plain text or JSON format.
            </p>
        </div>

        <div class="mb-6 md:mb-8 p-4 md:p-6 bg-gray-50 dark:bg-white rounded-lg">
            <h2 class="text-lg md:text-xl font-semibold mb-2">Base URL</h2>
            <div class="bg-gray-900 text-white p-2 md:p-3 rounded text-xs md:text-sm overflow-x-auto">
                <code class="whitespace-nowrap">{{ secure_url('/api') }}</code>
            </div>
        </div>

        <div class="mb-6 md:mb-8 p-4 md:p-6 bg-yellow-50 dark:bg-white border border-yellow-200 dark:border-gray-200 rounded-lg">
            <h2 class="text-lg md:text-xl font-semibold mb-2 text-yellow-800">Rate Limiting</h2>
            <p class="text-sm md:text-base text-yellow-700">All API endpoints are rate-limited to <strong>60 requests per minute</strong> per IP address.</p>
        </div>

        <!-- Endpoints -->
        <div class="space-y-8">
            <!-- Block Hash Endpoint -->
            <x-api-section method="GET" path="/blocks/tip/hash" :description="'Returns the hash of the current tip block of the Pepecoin blockchain.'" responseContentType="text/plain">
                <x-slot:example>
                    <code class="whitespace-nowrap">curl {{ route('api.blocks.tip.hash') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <code class="text-xs md:text-sm break-all">a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456</code>
                </x-slot:response>
            </x-api-section>

            <!-- Block Height Endpoint -->
            <x-api-section method="GET" path="/blocks/tip/height" :description="'Returns the current block height of the Pepecoin blockchain.'" responseContentType="text/plain">
                <x-slot:example>
                    <code>curl {{ route('api.blocks.tip.height') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <code class="text-sm">672216</code>
                </x-slot:response>
            </x-api-section>

            <!-- Blocks Window Endpoint -->
            <x-api-section method="GET" path="/api/blocks[/:startHeight]" :description="'Returns details on the past <strong>10 blocks</strong>.<br>If <code>:startHeight</code> is specified, the 10 blocks before (and including) <code>:startHeight</code> are returned.'" responseContentType="application/json">
                <x-slot:example>
                    <div class="whitespace-nowrap">
                        <code>curl {{ route('api.blocks.list') }}</code>
                    </div>
                    <div class="mt-2 whitespace-nowrap">
                        <code>curl {{ route('api.blocks.list', ['startHeight' => 600069]) }}</code>
                    </div>
                </x-slot:example>
                <x-slot:response>
<pre class="text-xs md:text-sm overflow-x-auto"><code>[
  {
    "id": "0000000000000000000384f28cb3...",
    "height": 600069,
    "version": 536870912,
    "timestamp": 1648829449,
    "tx_count": 1627,
    "size": 1210916,
    "difficulty": 28587155.782195,
    "nonce": 3580664066,
    "merkle_root": "efa344bcd6c0607f93b7..."
  },
  {
    "id": "0000000000000000000a1b2c3d4e...",
    "height": 600068,
    "version": 536870912,
    "timestamp": 1648828850,
    "tx_count": 1432,
    "size": 1180421,
    "difficulty": 28587155.782195,
    "nonce": 1532048855,
    "merkle_root": "ab12cd34ef56ab78cd90..."
  },
  ...
]</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="id" type="string" description="Block hash" />
                    <x-api-field name="height" type="integer" description="Block height" />
                    <x-api-field name="version" type="integer" description="Block version" />
                    <x-api-field name="timestamp" type="integer" description="Unix time the block was created" />
                    <x-api-field name="tx_count" type="integer" description="Number of transactions in the block" />
                    <x-api-field name="size" type="integer" description="Block size in bytes" />
                    <x-api-field name="difficulty" type="float" description="Current difficulty value for the block" />
                    <x-api-field name="nonce" type="integer" description="Nonce used for the block" />
                    <x-api-field name="merkle_root" type="string" description="Merkle root of the block" />
                </x-slot:fields>
            </x-api-section>

            <!-- Mempool Endpoint -->
            <x-api-section method="GET" path="/mempool" :description="'Returns a summary of the current mempool state, including the number of transactions and total size in bytes.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.mempool.index') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "count": 1245,
  "bytes": 1234567
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="count" type="integer" description="Number of transactions in the mempool" />
                    <x-api-field name="bytes" type="integer" description="Total size of all transactions in the mempool in bytes" />
                </x-slot:fields>
            </x-api-section>

            <!-- Mempool TXIDs Endpoint -->
            <x-api-section method="GET" path="/mempool/txids" :description="'Returns an array of transaction IDs currently in the mempool.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.mempool.txids') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>[
  "txid1...",
  "txid2...",
  "txid3..."
]</code></pre>
                </x-slot:response>
            </x-api-section>

            <!-- Prices Endpoint -->
            <x-api-section method="GET" path="/prices" :description="'Returns the latest Pepecoin prices.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.prices') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "time": 1724263564,
  "EUR": 0.00000123,
  "USD": 0.00000134
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="time" type="integer" description="Unix timestamp indicating when the price data was last refreshed" />
                    <x-api-field name="EUR" type="float" description="Latest price in Euro" />
                    <x-api-field name="USD" type="float" description="Latest price in US Dollar" />
                </x-slot:fields>
            </x-api-section>

            <!-- Address Validation Endpoint -->
            <x-api-section method="GET" path="/validate-address/:address" :description="'Validates a Pepecoin address and returns metadata.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.validate.address', ['address' => 'PbvihBLgz6cFJnhYscevB4n3o85faXPG7D']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "isvalid": true,
  "address": "PbvihBLgz6cFJnhYscevB4n3o85faXPG7D",
  "scriptPubKey": "76a914c825a1ecf2a6830c4401620c3a16f1995057c2ab88ac",
  "isscript": false
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="isvalid" type="boolean" description="Whether the address is valid" />
                    <x-api-field name="address" type="string" description="The normalized address" />
                    <x-api-field name="scriptPubKey" type="string|null" description="The scriptPubKey for the address, if available" />
                    <x-api-field name="isscript" type="boolean" description="True if the address is a script address" />
                </x-slot:fields>
            </x-api-section>
        </div>

        <!-- Error Responses -->
        <div class="mt-8 p-6 bg-red-50 dark:bg-white border border-red-200 dark:border-gray-200 rounded-lg">
            <h2 class="text-xl font-semibold mb-4 text-red-800">Error Responses</h2>
            <div class="space-y-6 md:space-y-8">
                <div>
                    <strong class="text-red-700">429 Too Many Requests:</strong>
                    <span class="text-red-600">Rate limit exceeded (60 requests per minute)</span>
                </div>
                <div>
                    <strong class="text-red-700">500 Internal Server Error:</strong>
                    <span class="text-red-600">Server error occurred while processing the request</span>
                </div>
            </div>
        </div>

</x-layout>
