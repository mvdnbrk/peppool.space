<x-layout title="PRC-721: Extended Inscription Envelope for Pepecoin" og_image="peppool-prc-721.png" og_description="PRC-721 is a draft extension for Pepecoin inscriptions that introduces parent/child provenance, delegate inscriptions, and structured metadata.">
    <article>
        <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-400">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-gray-300 mb-3 md:mb-4">PRC-721: Extended Inscription Envelope for Pepecoin</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm mb-6">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-medium">Status: Draft</span>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded font-medium">Version: 1.1</span>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded font-medium">Date: 2026-03-19</span>
                <a href="https://github.com/mvdnbrk/ord-pepecoin" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors font-medium">
                    <x-icon-github class="h-4 w-4" />
                    <span>View on GitHub</span>
                </a>
            </div>
        </div>

        <nav class="mb-12 p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Table of Contents</h2>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm">
                <li><a href="#abstract" class="text-green-700 dark:text-green-400 hover:underline">Abstract</a></li>
                <li><a href="#motivation" class="text-green-700 dark:text-green-400 hover:underline">Motivation</a></li>
                <li><a href="#existing-format" class="text-green-700 dark:text-green-400 hover:underline">Existing Envelope Format</a></li>
                <li><a href="#prc-721-extension" class="text-green-700 dark:text-green-400 hover:underline">PRC-721 Extension</a></li>
                <li><a href="#compression" class="text-green-700 dark:text-green-400 hover:underline">Compression</a></li>
                <li><a href="#script-sig-budget" class="text-green-700 dark:text-green-400 hover:underline">ScriptSig Space Budget</a></li>
                <li><a href="#backwards-compatibility" class="text-green-700 dark:text-green-400 hover:underline">Backwards Compatibility</a></li>
                <li><a href="#indexer-implementation" class="text-green-700 dark:text-green-400 hover:underline">Indexer Implementation</a></li>
            </ul>
        </nav>

        <div class="space-y-12 text-gray-800 dark:text-gray-300">
            <section id="abstract">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Abstract</h2>
                <p class="mb-4">
                    PRC-721 extends the existing Pepecoin inscription format (P2SH scriptSig with countdown) to support <strong>parent/child provenance</strong>, <strong>delegate inscriptions</strong>, <strong>structured properties</strong>, and <strong>compressed metadata</strong>, while maintaining full backwards compatibility with existing parsers and indexers.
                </p>
                <p class="mb-4">
                    Tags are appended <strong>after</strong> the body countdown reaches zero. Old parsers stop at countdown 0 and never see the tags — inscription numbers remain consistent across all indexer versions.
                </p>
                <p>
                    Inspired by <a href="https://github.com/ordinals/ord" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">ordinals/ord</a>, but designed specifically for non-SegWit scriptSig chains. PRC-721 uses string tag keys (<code>"parent"</code>, <code>"delegate"</code>) rather than numeric tags to avoid collision with countdown integers, and introduces compression and space optimizations tailored to Pepecoin's 1650-byte scriptSig limit.
                </p>
            </section>

            <section id="motivation">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Motivation</h2>
                <p class="mb-4">
                    Pepecoin inscriptions ("pepinals") currently support basic content embedding via the <code>"ord"</code> countdown envelope. This is sufficient for standalone inscriptions but lacks:
                </p>
                <ul class="list-disc ml-6 space-y-2 mb-4">
                    <li><strong>Provenance</strong> — no way to link child inscriptions to a parent (e.g., items in a collection)</li>
                    <li><strong>Delegates</strong> — no way to reference another inscription's content (e.g., 10,000 PFPs pointing to a shared base image, saving fees)</li>
                    <li><strong>Metadata</strong> — no extensible field for structured data</li>
                    <li><strong>Properties</strong> — no standard way to attach titles and traits to inscriptions for display and filtering by indexers and explorers</li>
                </ul>
                <p>
                    No scriptSig-based chain has cleanly implemented these features. PRC-721 solves this without breaking existing inscriptions or indexers.
                </p>
            </section>

            <section id="existing-format">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Existing Envelope Format</h2>
                <p class="mb-4">The current format uses a countdown from <code>npieces-1</code> to <code>0</code>:</p>
                <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_3 "ord"              # protocol marker
OP_PUSHNUM_N                      # npieces (total body chunks)
OP_PUSHBYTES_&lt;len&gt; &lt;content_type&gt; # MIME type
OP_PUSHNUM_&lt;N-1&gt;   &lt;chunk_1&gt;      # countdown N-1
OP_PUSHNUM_&lt;N-2&gt;   &lt;chunk_2&gt;      # countdown N-2
...
OP_PUSHNUM_1        &lt;chunk_N-1&gt;   # countdown 1
OP_PUSHBYTES_0      &lt;chunk_N&gt;     # countdown 0 (final chunk)</code></pre>
                </div>
                <p>Parsers read the countdown until it reaches 0, then stop. <strong>Any data after countdown 0 is ignored by current parsers.</strong></p>
            </section>

            <section id="prc-721-extension">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">PRC-721 Extension</h2>
                <h3 class="text-lg font-bold mb-3">Envelope Structure</h3>
                <p class="mb-4">PRC-721 adds an optional <strong>tag trailer</strong> after the final body chunk. The trailer consists of string-keyed tag/value pairs:</p>
                <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-6">
<pre><code>OP_PUSHBYTES_3 "ord"              # protocol marker
OP_PUSHNUM_N                      # npieces
OP_PUSHBYTES_&lt;len&gt; &lt;content_type&gt; # MIME type
OP_PUSHNUM_&lt;N-1&gt;   &lt;chunk_1&gt;      # body countdown
...
OP_PUSHBYTES_0      &lt;chunk_N&gt;     # countdown 0 (body complete)
# ──── tag trailer (optional, new parsers only) ────
OP_PUSHBYTES_6  "parent"          # tag key
OP_PUSHBYTES_36 &lt;parent_id&gt;       # tag value: 32-byte txid LE + 4-byte vout LE
OP_PUSHBYTES_8  "delegate"        # tag key
OP_PUSHBYTES_36 &lt;delegate_id&gt;     # tag value: 32-byte txid LE + 4-byte vout LE</code></pre>
                </div>

                <h3 class="text-lg font-bold mb-3">Tag Format</h3>
                <p class="mb-4">Each tag is a pair of consecutive pushes:</p>
                <div class="overflow-x-auto mb-6 bg-white dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="py-2 px-4 font-bold">Push</th>
                                <th class="py-2 px-4 font-bold">Content</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4">Tag key</td>
                                <td class="py-2 px-4">UTF-8 string identifying the tag (e.g., <code>"parent"</code>, <code>"delegate"</code>)</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4">Tag value</td>
                                <td class="py-2 px-4">Raw bytes, interpretation depends on the tag key</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>Unknown tags MUST be ignored by parsers (forward compatibility).</p>

                <h2 class="text-xl md:text-2xl font-bold mb-4 mt-12 text-gray-900 dark:text-gray-100" id="defined-tags">Defined Tags (v1)</h2>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-bold mb-2"><code>parent</code> — Parent Inscription</h3>
                        <p class="mb-4">Links this inscription as a child of an existing inscription (provenance/collections).</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"parent"</code> (6 bytes)</li>
                            <li><strong>Value:</strong> 36 bytes — inscription ID (32-byte txid LE + 4-byte output index LE)</li>
                            <li><strong>Repeatable:</strong> Yes — an inscription may have multiple parents</li>
                        </ul>
                        <p class="mb-4"><strong>Validation:</strong> The <strong>first reveal transaction</strong> in the inscription's chain MUST spend the parent inscription's UTXO as one of its inputs. The tag alone is not sufficient — the indexer must verify the spend (cryptographic provenance).</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-2"><code>delegate</code> — Delegate Inscription</h3>
                        <p class="mb-4">Points to another inscription whose content should be served in place of this one. Useful for collections where many inscriptions share the same visual content.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"delegate"</code> (8 bytes)</li>
                            <li><strong>Value:</strong> 36 bytes — inscription ID (32-byte txid LE + 4-byte output index LE)</li>
                            <li><strong>Repeatable:</strong> No — only the first <code>"delegate"</code> tag is used</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-2"><code>metadata</code> — Arbitrary Metadata</h3>
                        <p class="mb-4">Attach arbitrary structured data to an inscription using CBOR encoding. Metadata is intended for protocol-specific data not necessarily for display.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"metadata"</code>, <code>"metadata;br"</code> (Brotli-compressed)</li>
                            <li><strong>Value:</strong> CBOR or JSON encoded bytes</li>
                            <li><strong>Size:</strong> Recommended under 1 KB</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-2"><code>properties</code> — Protocol-Level Properties</h3>
                        <p class="mb-4">Structured fields that the indexer treats as first-class attributes (e.g., <code>title</code>, <code>traits</code>). Indexers use this for display and filtering.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"properties"</code>, <code>"properties;br"</code> (Brotli-compressed)</li>
                            <li><strong>Value:</strong> CBOR-encoded bytes only</li>
                        </ul>
                        <div class="overflow-x-auto my-6 bg-white dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-600">
                                        <th class="py-2 px-4 font-bold">Key</th>
                                        <th class="py-2 px-4 font-bold">Type</th>
                                        <th class="py-2 px-4 font-bold">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 px-4"><code>title</code></td>
                                        <td class="py-2 px-4">string</td>
                                        <td class="py-2 px-4">Inscription title (displayed by indexer)</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4"><code>traits</code></td>
                                        <td class="py-2 px-4">map</td>
                                        <td class="py-2 px-4">Collection traits for filtering</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold mb-2"><code>content-encoding</code> — Body Compression</h3>
                        <p class="mb-4">Indicates that the inscription body is compressed (e.g., <code>br</code> or <code>gzip</code>).</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"content-encoding"</code> (16 bytes)</li>
                            <li><strong>Value:</strong> UTF-8 string — <code>"br"</code> or <code>"gzip"</code></li>
                        </ul>
                        <p class="mt-4">The indexer serves the compressed bytes with the corresponding HTTP <code>Content-Encoding</code> header.</p>
                    </div>
                </div>

                <h2 class="text-xl md:text-2xl font-bold mb-4 mt-12 text-gray-900 dark:text-gray-100" id="multi-tx-chains">Multi-Transaction Chains</h2>
                <p class="mb-4">For inscriptions that span multiple reveal transactions:</p>
                <ul class="list-disc ml-6 space-y-2">
                    <li>The <strong>body chunks</strong> are distributed across all transactions in the chain</li>
                    <li>The <strong>tag trailer</strong> goes exclusively in the <strong>final reveal transaction</strong></li>
                    <li>The <strong>parent UTXO</strong> (if any) is spent as an input in the <strong>first reveal transaction</strong></li>
                </ul>
            </section>

            <section id="compression">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Compression</h2>
                <h3 class="text-lg font-bold mb-3">The <code>;br</code> Suffix Pattern</h3>
                <p class="mb-4">Instead of using a separate encoding tag, PRC-721 encodes the compression method directly in the tag key name using a <code>;br</code> suffix:</p>
                <div class="overflow-x-auto mb-6 bg-white dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="py-2 px-4 font-bold">Tag Key</th>
                                <th class="py-2 px-4 font-bold">Encoding</th>
                                <th class="py-2 px-4 font-bold">Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>"metadata"</code></td>
                                <td class="py-2 px-4">Raw CBOR</td>
                                <td class="py-2 px-4">Arbitrary protocol data</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>"metadata;br"</code></td>
                                <td class="py-2 px-4">Brotli CBOR</td>
                                <td class="py-2 px-4">Compressed protocol data</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>"properties"</code></td>
                                <td class="py-2 px-4">Raw CBOR</td>
                                <td class="py-2 px-4">Structured identity</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4"><code>"properties;br"</code></td>
                                <td class="py-2 px-4">Brotli CBOR</td>
                                <td class="py-2 px-4">Compressed identity</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="script-sig-budget">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">ScriptSig Space Budget</h2>
                <p class="mb-4">
                    Pepecoin uses P2SH scriptSig inscriptions without SegWit. The <code>IsStandard</code> policy limits scriptSig to <strong>1650 bytes</strong>. After signature and redeem script overhead, roughly <strong>1500 bytes</strong> are available for inscription payload per reveal transaction.
                </p>
                <p>
                    PRC-721 tags (parent, properties, metadata) are appended after the final body chunk. They add minimal overhead, often fitting in the same final reveal transaction.
                </p>
            </section>

            <section id="backwards-compatibility">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Backwards Compatibility</h2>
                <p class="mb-4">
                    PRC-721 is designed to be maximally backwards compatible. Old parsers (without tag support) will process the body countdown to 0 and <strong>stop reading</strong> — they never see the tag trailer.
                </p>
                <p>
                    Inscription numbers remain consistent across all indexer versions because PRC-721 inscriptions are never skipped; only the additional metadata is ignored by older parsers.
                </p>
            </section>

            <section id="indexer-implementation">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Indexer Implementation</h2>
                <p class="mb-4">New tables required for indexing PRC-721 metadata:</p>
                <div class="overflow-x-auto mb-6 bg-white dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                <th class="py-2 px-4 font-bold">Table</th>
                                <th class="py-2 px-4 font-bold">Key</th>
                                <th class="py-2 px-4 font-bold">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>INSCRIPTION_ID_TO_PARENT</code></td>
                                <td class="py-2 px-4">InscriptionId</td>
                                <td class="py-2 px-4">Vec&lt;InscriptionId&gt;</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>PARENT_TO_CHILDREN</code></td>
                                <td class="py-2 px-4">InscriptionId</td>
                                <td class="py-2 px-4">Vec&lt;InscriptionId&gt;</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>INSCRIPTION_ID_TO_DELEGATE</code></td>
                                <td class="py-2 px-4">InscriptionId</td>
                                <td class="py-2 px-4">InscriptionId</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4"><code>INSCRIPTION_ID_TO_METADATA</code></td>
                                <td class="py-2 px-4">InscriptionId</td>
                                <td class="py-2 px-4">Vec&lt;u8&gt; (raw CBOR)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </article>
</x-layout>