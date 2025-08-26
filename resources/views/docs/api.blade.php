<x-layout title="Pepecoin API Documentation" og_image="pepecoin-api.png">
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
            <code class="text-xs md:text-sm bg-white px-2 md:px-3 py-1 rounded border break-all">{{ url('/api') }}</code>
        </div>

        <div class="mb-6 md:mb-8 p-4 md:p-6 bg-yellow-50 dark:bg-white border border-yellow-200 dark:border-gray-200 rounded-lg">
            <h2 class="text-lg md:text-xl font-semibold mb-2 text-yellow-800">Rate Limiting</h2>
            <p class="text-sm md:text-base text-yellow-700">All API endpoints are rate-limited to <strong>60 requests per minute</strong> per IP address.</p>
        </div>

        <!-- Endpoints -->
        <div class="space-y-8">
            <!-- Block Hash Endpoint -->
            <div class="border border-gray-200 rounded-lg p-4 md:p-6 bg-white">
                <div class="flex flex-wrap items-center mb-3 md:mb-4 gap-2">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded">GET</span>
                    <code class="text-base md:text-lg font-mono break-all">/blocks/tip/hash</code>
                </div>

                <p class="text-gray-600 mb-4">Returns the hash of the current tip block of the Pepecoin blockchain.</p>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Example</h4>
                    <div class="bg-gray-900 text-white p-2 md:p-3 rounded text-xs md:text-sm overflow-x-auto">
                        <code class="whitespace-nowrap">curl {{ route('api.blocks.tip.hash') }}</code>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response</h4>
                    <div class="bg-gray-50 p-2 md:p-3 rounded border overflow-x-auto">
                        <p class="text-xs md:text-sm text-gray-600 mb-1">Content-Type: text/plain</p>
                        <code class="text-xs md:text-sm break-all">a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456</code>
                    </div>
                </div>
            </div>

            <!-- Block Height Endpoint -->
            <div class="border border-gray-200 rounded-lg p-6 bg-white">
                <div class="flex items-center mb-4">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">GET</span>
                    <code class="text-lg font-mono">/blocks/tip/height</code>
                </div>

                <p class="text-gray-600 mb-4">Returns the current block height of the Pepecoin blockchain.</p>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Example</h4>
                    <div class="bg-gray-900 text-white p-3 rounded text-sm">
                        <code>curl {{ route('api.blocks.tip.height') }}</code>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response</h4>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-sm text-gray-600 mb-1">Content-Type: text/plain</p>
                        <code class="text-sm">672216</code>
                    </div>
                </div>
            </div>

            <!-- Mempool Endpoint -->
            <div class="border border-gray-200 rounded-lg p-6 bg-white">
                <div class="flex items-center mb-4">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">GET</span>
                    <code class="text-lg font-mono">/mempool</code>
                </div>

                <p class="text-gray-600 mb-4">Returns a summary of the current mempool state, including the number of transactions and total size in bytes.</p>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Example</h4>
                    <div class="bg-gray-900 text-white p-3 rounded text-sm">
                        <code>curl {{ route('api.mempool.index') }}</code>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response</h4>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-sm text-gray-600 mb-1">Content-Type: application/json</p>
                        <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "count": 1245,
  "bytes": 1234567
}</code></pre>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response Fields</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs md:text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 font-semibold">Field</th>
                                    <th class="text-left py-2 font-semibold">Type</th>
                                    <th class="text-left py-2 font-semibold">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-2"><code>count</code></td>
                                    <td class="py-2">integer</td>
                                    <td class="py-2">Number of transactions in the mempool</td>
                                </tr>
                                <tr>
                                    <td class="py-2"><code>bytes</code></td>
                                    <td class="py-2">integer</td>
                                    <td class="py-2">Total size of all transactions in the mempool in bytes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mempool TXIDs Endpoint -->
            <div class="border border-gray-200 rounded-lg p-6 bg-white">
                <div class="flex items-center mb-4">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">GET</span>
                    <code class="text-lg font-mono">/mempool/txids</code>
                </div>

                <p class="text-gray-600 mb-4">Returns an array of transaction IDs currently in the mempool.</p>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Example</h4>
                    <div class="bg-gray-900 text-white p-3 rounded text-sm">
                        <code>curl {{ route('api.mempool.txids') }}</code>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response</h4>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-sm text-gray-600 mb-1">Content-Type: application/json</p>
                        <pre class="text-xs md:text-sm overflow-x-auto"><code>[
  "txid1...",
  "txid2...",
  "txid3..."
]</code></pre>
                    </div>
                </div>
            </div>

            <!-- Prices Endpoint -->
            <div class="border border-gray-200 rounded-lg p-6 bg-white">
                <div class="flex items-center mb-4">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-3">GET</span>
                    <code class="text-lg font-mono">/prices</code>
                </div>

                <p class="text-gray-600 mb-4">Returns the latest Pepecoin prices.</p>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Example</h4>
                    <div class="bg-gray-900 text-white p-3 rounded text-sm">
                        <code>curl {{ route('api.prices') }}</code>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response</h4>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-sm text-gray-600 mb-1">Content-Type: application/json</p>
                        <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "time": 1724263564,
  "EUR": 0.00000123,
  "USD": 0.00000134
}</code></pre>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Response Fields</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs md:text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 font-semibold">Field</th>
                                    <th class="text-left py-2 font-semibold">Type</th>
                                    <th class="text-left py-2 font-semibold">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="py-2"><code>time</code></td>
                                    <td class="py-2">integer</td>
                                    <td class="py-2">Unix timestamp indicating when the price data was last refreshed</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="py-2"><code>EUR</code></td>
                                    <td class="py-2">float</td>
                                    <td class="py-2">Latest price in Euro</td>
                                </tr>
                                <tr>
                                    <td class="py-2"><code>USD</code></td>
                                    <td class="py-2">float</td>
                                    <td class="py-2">Latest price in US Dollar</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
