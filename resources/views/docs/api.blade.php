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
            <x-api-section method="GET" path="/blocks/tip/hash" :description="'Returns the hash of the current tip block of the Pepecoin blockchain.'" responseContentType="application/json">
                <x-slot:example>
                    <code class="whitespace-nowrap">curl {{ route('api.blocks.tip.hash') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "hash": "a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456"
}</code></pre>
                </x-slot:response>
            </x-api-section>

            <!-- Block Height Endpoint -->
            <x-api-section method="GET" path="/blocks/tip/height" :description="'Returns the current block height of the Pepecoin blockchain.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.blocks.tip.height') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "height": 672216
}</code></pre>
                </x-slot:response>
            </x-api-section>

            <!-- Blocks Window Endpoint -->
            <x-api-section method="GET" path="/blocks[/:startHeight]" :description="'Returns details on the past <strong>10 blocks</strong>.<br>If <code>:startHeight</code> is specified, the 10 blocks before (and including) <code>:startHeight</code> are returned.'" responseContentType="application/json">
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
    "version": 1,
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
    "version": 1,
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

            <!-- Mempool Recent Endpoint -->
            <x-api-section method="GET" path="/mempool/recent" :description="'Returns a list of the last 10 transactions to enter the mempool. Each transaction object contains simplified overview data.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.mempool.recent') }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>[
  {
    "txid": "4b93c138293a7e3dfea6f0a63d944890b5ba571b03cc22d8c66995535e90dce8",
    "fee": 18277,
    "vsize": 2585,
    "value": 4972029
  },
  ...
]</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="txid" type="string" description="Transaction ID" />
                    <x-api-field name="fee" type="integer" description="Transaction fee in ribbits" />
                    <x-api-field name="vsize" type="integer" description="Virtual size of the transaction" />
                    <x-api-field name="value" type="integer" description="Total output value in ribbits" />
                </x-slot:fields>
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

            <!-- Address Endpoint -->
            <x-api-section method="GET" path="/address/:address" :description="'Returns details about a Pepecoin address, including chain and mempool statistics.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.address.show', ['address' => 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "address": "PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M",
  "chain_stats": {
    "funded_txo_count": 1,
    "funded_txo_sum": 100000000,
    "spent_txo_count": 0,
    "spent_txo_sum": 0,
    "tx_count": 1
  },
  "mempool_stats": {
    "funded_txo_count": 0,
    "funded_txo_sum": 0,
    "spent_txo_count": 0,
    "spent_txo_sum": 0,
    "tx_count": 0
  }
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="address" type="string" description="The address" />
                    <x-api-field name="chain_stats" type="object" description="Statistics for confirmed transactions" />
                    <x-api-field name="mempool_stats" type="object" description="Statistics for unconfirmed transactions" />
                </x-slot:fields>
            </x-api-section>

            <!-- Address Transactions Endpoint -->
            <x-api-section method="GET" path="/address/:address/txs" :description="'Returns a list of transactions for a Pepecoin address.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.address.transactions', ['address' => 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>[
  {
    "txid": "dba43fd04b7ae3df8e5b596f2e7fab247c58629d622e3a5213f03a5a09684430",
    "version": 1,
    "locktime": 0,
    "vin": [ ... ],
    "vout": [ ... ],
    "size": 255,
    "weight": 1020,
    "fee": 10000,
    "status": {
      "confirmed": true,
      "block_height": 326148,
      "block_hash": "00000000000000001e4118adcfbb02364bc13c41c210d8811e4f39aeb3687e36",
      "block_time": 1413798020
    }
  },
  ...
]</code></pre>
                </x-slot:response>
            </x-api-section>

            <!-- Address UTXO Endpoint -->
            <x-api-section method="GET" path="/address/:address/utxo" :description="'Returns the unspent transaction outputs (UTXOs) for a Pepecoin address.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.address.utxo', ['address' => 'PumNFmkevCTG6RTEc7W2piGTbQHMg2im2M']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>[
  {
    "txid": "58ed78527f8c2fc7e745d18c72978e6aaeb450b4816472a841d2d6453b6accb1",
    "vout": 0,
    "status": {
      "confirmed": true,
      "block_height": 916697,
      "block_hash": "a991281771fb38bc5a0ac0b8a3872451c243fddd49116a3805a78a58c24620aa",
      "block_time": 1771080551
    },
    "value": 100000000
  },
  {
    "txid": "9fcd620fff32eff8d9d48de65100501098d48eb175ad993d44c434ff7e462756",
    "vout": 1,
    "status": {
      "confirmed": true,
      "block_height": 916835,
      "block_hash": "a1b0da083051e4a2c06eb2f5fdffd950b8ca0cb14672b72d908e928340cd1737",
      "block_time": 1771089008
    },
    "value": 100000000
  }
]</code></pre>
                </x-slot:response>
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

            <!-- Transaction Endpoint -->
            <x-api-section method="GET" path="/tx/:txid" :description="'Returns details about a transaction.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.tx.show', ['txid' => '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "txid": "2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261",
  "version": 1,
  "locktime": 0,
  "vin": [...],
  "vout": [...],
  "size": 221,
  "weight": 557,
  "fee": 19,
  "status": {
    "confirmed": true,
    "block_height": 936511,
    "block_hash": "0000000000000000000222c4ff88dc74fb21daa72d326bbcabb2b97413dacb7a",
    "block_time": 1771054926
  }
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="txid" type="string" description="Transaction ID" />
                    <x-api-field name="version" type="integer" description="Transaction version" />
                    <x-api-field name="locktime" type="integer" description="Transaction locktime" />
                    <x-api-field name="size" type="integer" description="Transaction size in bytes" />
                    <x-api-field name="fee" type="integer" description="Transaction fee in ribbits" />
                    <x-api-field name="status" type="object" description="Confirmation status and block info" />
                </x-slot:fields>
            </x-api-section>

            <!-- Transaction Status Endpoint -->
            <x-api-section method="GET" path="/tx/:txid/status" :description="'Returns the confirmation status of a transaction.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.tx.status', ['txid' => '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "confirmed": true,
  "block_height": 936511,
  "block_hash": "0000000000000000000222c4ff88dc74fb21daa72d326bbcabb2b97413dacb7a",
  "block_time": 1771054926
}</code></pre>
                </x-slot:response>
                <x-slot:fields>
                    <x-api-field name="confirmed" type="boolean" description="Whether the transaction is confirmed" />
                    <x-api-field name="block_height" type="integer" description="Height of the block containing the transaction (if confirmed)" />
                    <x-api-field name="block_hash" type="string" description="Hash of the block containing the transaction (if confirmed)" />
                    <x-api-field name="block_time" type="integer" description="Unix time the block was created (if confirmed)" />
                </x-slot:fields>
            </x-api-section>

            <!-- Transaction Hex Endpoint -->
            <x-api-section method="GET" path="/tx/:txid/hex" :description="'Returns the raw transaction hex.'" responseContentType="application/json">
                <x-slot:example>
                    <code>curl {{ route('api.tx.hex', ['txid' => '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <pre class="text-xs md:text-sm overflow-x-auto"><code>{
  "hex": "010000000536a007284bd52ee826680a7f43536472f1bcce1e76cd76b826b88c5884eddf1f0c0000006b483045022100bcdf40fb3b5ebfa2c158ac8d1a41c03eb3dba4e180b00e81836bafd56d946efd022005cc40e35022b614275c1e485c409599667cbd41f6e5d78f421cb260a020a24f01210255ea3f53ce3ed1ad2c08dfc23b211b15b852afb819492a9a0f3f99e5747cb5f0ffffffffee08cb90c4e84dd7952b2cfad81ed3b088f5b..."
}</code></pre>
                </x-slot:response>
            </x-api-section>

            <!-- Transaction Raw Endpoint -->
            <x-api-section method="GET" path="/tx/:txid/raw" :description="'Returns the transaction as binary data.'" responseContentType="application/octet-stream">
                <x-slot:example>
                    <code>curl {{ route('api.tx.raw', ['txid' => '2c603d097588bb7d520ffb8b270cc61865f52c1427504ab43678fc055d07c261']) }}</code>
                </x-slot:example>
                <x-slot:response>
                    <code class="text-xs md:text-sm break-all">&lt;binary data&gt;</code>
                </x-slot:response>
            </x-api-section>
        </div>

        <!-- Error Responses -->
        <div class="mt-8 p-6 bg-red-50 dark:bg-white border border-red-200 dark:border-gray-200 rounded-lg">
            <h2 class="text-xl font-semibold mb-4 text-red-800">Error Responses</h2>
            <div class="space-y-6">
                <p class="text-sm md:text-base text-gray-700 mb-4">
                    Errors are returned as JSON objects with an <code>error</code> code and a human-readable <code>message</code>.
                </p>
                <div class="bg-gray-900 text-white p-4 rounded-lg">
                    <pre class="text-xs md:text-sm"><code>{
  "code": 404,
  "error": "not_found",
  "message": "The requested resource could not be found."
}</code></pre>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <strong class="text-red-700 text-sm block">400 Bad Request</strong>
                        <span class="text-red-600 text-xs">Invalid parameters or malformed request</span>
                    </div>
                    <div>
                        <strong class="text-red-700 text-sm block">404 Not Found</strong>
                        <span class="text-red-600 text-xs">Resource does not exist</span>
                    </div>
                    <div>
                        <strong class="text-red-700 text-sm block">429 Too Many Requests</strong>
                        <span class="text-red-600 text-xs">Rate limit exceeded (60 req/min)</span>
                    </div>
                    <div>
                        <strong class="text-red-700 text-sm block">500 Internal Error</strong>
                        <span class="text-red-600 text-xs">Unexpected server error</span>
                    </div>
                </div>
            </div>
        </div>

</x-layout>
