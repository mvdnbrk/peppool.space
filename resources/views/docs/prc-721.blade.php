<x-layout title="PRC-721: Extended Inscription Envelope for Pepecoin" og_image="peppool-prc-721.png" og_description="PRC-721 is a draft extension for Pepecoin inscriptions that introduces parent/child provenance, delegate inscriptions, and structured metadata.">
    <article>
        <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-400">
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-gray-300 mb-3 md:mb-4">PRC-721: Extended Inscription Envelope for Pepecoin</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm mb-6">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-medium">Status: Draft</span>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded font-medium">Version: 1.3</span>
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded font-medium">Date: 2026-03-24</span>
                <a href="https://github.com/mvdnbrk/ord-pepecoin" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors font-medium">
                    <x-icon-github class="h-4 w-4" />
                    <span>View on GitHub</span>
                </a>
            </div>
        </div>

        <div class="mb-12 grid grid-cols-1 md:grid-cols-2 gap-6">
            <nav class="p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Table of Contents</h2>
                <ol class="list-decimal ml-6 space-y-2 text-sm">
                    <li><a href="#abstract" class="text-green-700 dark:text-green-400 hover:underline">Abstract</a></li>
                    <li><a href="#motivation" class="text-green-700 dark:text-green-400 hover:underline">Motivation</a></li>
                    <li><a href="#existing-format" class="text-green-700 dark:text-green-400 hover:underline">Existing Envelope Format</a></li>
                    <li><a href="#prc-721-extension" class="text-green-700 dark:text-green-400 hover:underline">PRC-721 Extension</a></li>
                    <li><a href="#defined-tags" class="text-green-700 dark:text-green-400 hover:underline">Defined Tags</a></li>
                    <li><a href="#multi-tx-chains" class="text-green-700 dark:text-green-400 hover:underline">Multi-Transaction Chains</a></li>
                    <li><a href="#compression" class="text-green-700 dark:text-green-400 hover:underline">Compression</a></li>
                    <li><a href="#script-sig-budget" class="text-green-700 dark:text-green-400 hover:underline">ScriptSig Space Budget</a></li>
                    <li><a href="#backwards-compatibility" class="text-green-700 dark:text-green-400 hover:underline">Backwards Compatibility</a></li>
                    <li><a href="#validation-rules" class="text-green-700 dark:text-green-400 hover:underline">Validation Rules</a></li>
                    <li><a href="#parser-pseudocode" class="text-green-700 dark:text-green-400 hover:underline">Parser Pseudocode</a></li>
                    <li><a href="#extensibility" class="text-green-700 dark:text-green-400 hover:underline">Extensibility</a></li>
                    <li><a href="#references" class="text-green-700 dark:text-green-400 hover:underline">References</a></li>
                </ol>
            </nav>
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600">
                            <th class="py-2 px-4 font-bold text-gray-900 dark:text-gray-100">Feature</th>
                            <th class="py-2 px-4 font-bold text-gray-900 dark:text-gray-100">Spec</th>
                            <th class="py-2 px-4 font-bold text-gray-900 dark:text-gray-100">Indexer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 px-4"><a href="#tag-parent" class="text-green-700 dark:text-green-400 hover:underline">Parent/child</a></td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 px-4"><a href="#tag-delegate" class="text-green-700 dark:text-green-400 hover:underline">Delegate</a></td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 px-4"><a href="#tag-metadata" class="text-green-700 dark:text-green-400 hover:underline">Metadata</a></td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                            <td class="py-2 px-4 text-gray-400">—</td>
                        </tr>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-2 px-4"><a href="#tag-properties" class="text-green-700 dark:text-green-400 hover:underline">Properties</a></td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4"><a href="#tag-content-encoding" class="text-green-700 dark:text-green-400 hover:underline">Body compression</a></td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                            <td class="py-2 px-4 text-green-600 dark:text-green-400">&#10003;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-12 text-gray-800 dark:text-gray-300">
            <section id="abstract">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Abstract</h2>
                <p class="mb-4">
                    This specification is developed as part of <a href="https://github.com/mvdnbrk/ord-pepecoin" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">ord-pepecoin</a>, the reference indexer for Pepecoin inscriptions.
                </p>
                <p class="mb-4">
                    PRC-721 extends the existing Pepecoin inscription format (P2SH scriptSig with countdown) to support <strong>parent/child provenance</strong>, <strong>delegate inscriptions</strong>, <strong>structured properties</strong>, and <strong>compressed metadata</strong>, while maintaining full backwards compatibility with existing parsers and indexers.
                </p>
                <p class="mb-4">
                    Tags are appended <strong>after</strong> the body countdown reaches zero. Old parsers stop at countdown 0 and never see the tags — inscription numbers remain consistent across all indexer versions.
                </p>
                <p class="mb-6">
                    Inspired by <a href="https://github.com/ordinals/ord" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">ordinals/ord</a>, but designed specifically for non-SegWit scriptSig chains. PRC-721 uses string tag keys (<code>"parent"</code>, <code>"delegate"</code>) rather than numeric tags to avoid collision with countdown integers, and introduces compression and space optimizations tailored to Pepecoin's 1650-byte scriptSig limit.
                </p>

            </section>

            <section id="motivation">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Motivation</h2>
                <p class="mb-4">
                    Pepecoin inscriptions currently support basic content embedding via the <code>"ord"</code> countdown envelope. This is sufficient for standalone inscriptions but lacks:
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

                <h2 class="text-xl md:text-2xl font-bold mb-4 mt-12 text-gray-900 dark:text-gray-100" id="defined-tags">Defined Tags</h2>

                <div class="space-y-8">
                    <div>
                        <h4 class="text-lg font-bold mb-2" id="tag-parent"><code>parent</code> — Parent Inscription</h4>
                        <p class="mb-4">Links this inscription as a child of an existing inscription (provenance/collections).</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"parent"</code> (6 bytes)</li>
                            <li><strong>Value:</strong> 36 bytes — inscription ID (32-byte txid little-endian + 4-byte output index little-endian)</li>
                            <li><strong>Repeatable:</strong> Yes — an inscription may have multiple parents</li>
                            <li><strong>Validation:</strong> The <strong>first reveal transaction</strong> in the inscription's chain MUST spend the parent inscription's UTXO as one of its inputs. The tag alone is not sufficient — the indexer must verify the spend (cryptographic provenance).</li>
                        </ul>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_6  "parent"
OP_PUSHBYTES_36 &lt;32-byte txid LE&gt;&lt;4-byte vout LE&gt;</code></pre>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-2" id="tag-delegate"><code>delegate</code> — Delegate Inscription</h4>
                        <p class="mb-4">Points to another inscription whose content should be served in place of this one. Useful for collections where many inscriptions share the same visual content.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"delegate"</code> (8 bytes)</li>
                            <li><strong>Value:</strong> 36 bytes — inscription ID (32-byte txid little-endian + 4-byte output index little-endian)</li>
                            <li><strong>Repeatable:</strong> No — only the first <code>"delegate"</code> tag is used</li>
                            <li><strong>Behavior:</strong> When serving <code>/content/&lt;id&gt;</code>, if the inscription has a delegate, the indexer serves the delegate's content and content type. If the delegate is missing, returns 404.</li>
                        </ul>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_8  "delegate"
OP_PUSHBYTES_36 &lt;32-byte txid LE&gt;&lt;4-byte vout LE&gt;</code></pre>
                        </div>
                        <p class="mb-4">A delegate inscription has no body — it is empty (<code>npieces=0</code>):</p>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code># Delegate inscription (no body)
OP_PUSHBYTES_3  "ord"
OP_PUSHBYTES_0                    # npieces = 0
OP_PUSHBYTES_0                    # empty content_type
OP_PUSHBYTES_8  "delegate"
OP_PUSHBYTES_36 &lt;delegate_id&gt;</code></pre>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-2" id="tag-metadata"><code>metadata</code> — Arbitrary Metadata</h4>
                        <p class="mb-4">Attach arbitrary structured data to an inscription using CBOR encoding. Metadata is intended for protocol-specific or metaprotocol data (e.g., PRC-20 state) that external tools may consume. It is <strong>not</strong> used by the indexer for display — use <code>"properties"</code> for that.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"metadata"</code>, <code>"metadata;br"</code> (Brotli-compressed)</li>
                            <li><strong>Value:</strong> CBOR or JSON encoded bytes</li>
                            <li><strong>Repeatable:</strong> No — first occurrence wins, duplicates are ignored</li>
                            <li><strong>Size:</strong> Recommended under 1 KB</li>
                        </ul>
                        <p class="mb-4">CBOR is preferred for efficiency. JSON is also accepted — the parser distinguishes by checking the first byte: <code>{</code> or <code>[</code> indicates JSON, otherwise CBOR. This allows simple inscribers to attach metadata without a CBOR library, at the cost of a few extra bytes.</p>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_8  "metadata"
OP_PUSHDATA1    &lt;CBOR or JSON bytes&gt;</code></pre>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-2" id="tag-properties"><code>properties</code> — Protocol-Level Properties</h4>
                        <p class="mb-4">Structured fields that the indexer treats as first-class attributes of the inscription. Separate from <code>"metadata"</code> so the indexer knows exactly where to find protocol-recognized fields without parsing through arbitrary user data.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"properties"</code>, <code>"properties;br"</code> (Brotli-compressed)</li>
                            <li><strong>Value:</strong> CBOR-encoded bytes only (RFC 8949) — JSON is not accepted</li>
                            <li><strong>Repeatable:</strong> No — first occurrence wins</li>
                        </ul>

                        <p class="mb-4">The properties CBOR map uses <strong>integer keys</strong> for compactness:</p>

                        <div class="overflow-x-auto my-6 bg-white dark:bg-gray-800/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-600">
                                        <th class="py-2 px-4 font-bold">Key</th>
                                        <th class="py-2 px-4 font-bold">CBOR integer key</th>
                                        <th class="py-2 px-4 font-bold">Type</th>
                                        <th class="py-2 px-4 font-bold">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 px-4">title</td>
                                        <td class="py-2 px-4"><code>0</code></td>
                                        <td class="py-2 px-4">string</td>
                                        <td class="py-2 px-4">Inscription title</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4">traits</td>
                                        <td class="py-2 px-4"><code>1</code></td>
                                        <td class="py-2 px-4">map&lt;string, value&gt;</td>
                                        <td class="py-2 px-4">Inscription traits</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="mb-4">
                            Trait values may be <strong>strings</strong>, <strong>booleans</strong>, <strong>integers</strong>, or <strong>null</strong>. Other CBOR types (floats, bytes, arrays, maps) are rejected — the entire <code>properties</code> tag is ignored if any trait value uses an unsupported type. Trait names must be unique — duplicate names invalidate the <code>properties</code> tag. Trait order is preserved — indexers and explorers display traits in the order they appear in the CBOR map, allowing inscribers to control display priority.
                        </p>

                        <p class="mb-2 text-sm font-bold">Example CBOR value (diagnostic notation):</p>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>{0: "Rare Pepe #42", 1: {"background": "gold", "eyes": "laser", "mouth": "grin", "accessory": "crown", "level": 42, "rare": true, "extra": null}}</code></pre>
                        </div>

                        <p class="mb-2 text-sm font-bold">Equivalent JSON for readability:</p>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>{
  "title": "Rare Pepe #42",
  "traits": {
    "background": "gold",
    "eyes": "laser",
    "mouth": "grin",
    "accessory": "crown",
    "level": 42,
    "rare": true,
    "extra": null
  }
}</code></pre>
                        </div>

                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_10 "properties"
OP_PUSHDATA1    &lt;CBOR bytes&gt;</code></pre>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mt-4">
                            <h5 class="font-bold text-yellow-800 dark:text-yellow-400 mb-2">Why both <code>metadata</code> and <code>properties</code>?</h5>
                            <ul class="list-disc ml-6 space-y-1 text-sm text-yellow-800 dark:text-yellow-400">
                                <li><strong>Properties</strong> contain the <strong>public identity</strong> of the inscription — name and traits. Indexers and explorers use this directly for display and filtering.</li>
                                <li><strong>Metadata</strong> is for <strong>protocol data</strong> — arbitrary key/value pairs consumed by external tools or metaprotocols, not necessarily part of the visual identity.</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-bold mb-2" id="tag-content-encoding"><code>content-encoding</code> — Body Compression</h4>
                        <p class="mb-4">Indicates that the inscription body is compressed. The indexer decompresses the body before serving <code>/content/</code>.</p>
                        <ul class="list-disc ml-6 space-y-1 mb-4">
                            <li><strong>Key:</strong> <code>"content-encoding"</code> (16 bytes)</li>
                            <li><strong>Value:</strong> UTF-8 string — <code>"br"</code> (Brotli) or <code>"gzip"</code></li>
                            <li><strong>Repeatable:</strong> No — first occurrence wins</li>
                        </ul>
                        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>OP_PUSHBYTES_16 "content-encoding"
OP_PUSHBYTES_2  "br"</code></pre>
                        </div>
                        <p class="mt-4">The indexer does <strong>not</strong> decompress the body. It stores the compressed bytes as-is and serves them with the corresponding HTTP <code>Content-Encoding</code> header (e.g., <code>Content-Encoding: br</code>). The browser handles decompression natively. This avoids decompression bomb risks at the server level.</p>
                    </div>
                </div>

                <h2 class="text-xl md:text-2xl font-bold mb-4 mt-12 text-gray-900 dark:text-gray-100" id="multi-tx-chains">Multi-Transaction Chains</h2>
                <p class="mb-4">For inscriptions that span multiple reveal transactions:</p>
                <ul class="list-disc ml-6 space-y-2">
                    <li>The <strong>body chunks</strong> are distributed across all transactions in the chain (as today)</li>
                    <li>The <strong>tag trailer</strong> (tags) goes exclusively in the <strong>final reveal transaction</strong>, after the last body chunk (countdown 0)</li>
                    <li>The <strong>parent UTXO</strong> (if any) is spent as an input in the <strong>first reveal transaction</strong></li>
                    <li>If the final reveal transaction has no tags after countdown 0, the inscription has no PRC-721 features (standard inscription)</li>
                    <li>Parent spend validation only applies to the first reveal transaction — intermediate chain transactions are not checked</li>
                </ul>
            </section>

            <section id="compression">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Compression</h2>
                <h3 class="text-lg font-bold mb-3">The <code>;br</code> Suffix Pattern</h3>
                <p class="mb-4">Instead of using a separate encoding tag (which costs additional bytes), PRC-721 encodes the compression method directly in the tag key name using a <code>;br</code> suffix:</p>
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
                                <td class="py-2 px-4">Brotli-compressed CBOR</td>
                                <td class="py-2 px-4">Compressed protocol data</td>
                            </tr>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 px-4"><code>"properties"</code></td>
                                <td class="py-2 px-4">Raw CBOR</td>
                                <td class="py-2 px-4">Structured identity (title, traits)</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-4"><code>"properties;br"</code></td>
                                <td class="py-2 px-4">Brotli-compressed CBOR</td>
                                <td class="py-2 px-4">Compressed structured identity</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h3 class="text-lg font-bold mb-3">Parser Behavior</h3>
                <ol class="list-decimal ml-6 space-y-2 mb-6">
                    <li>If the tag key ends with <code>";br"</code>, decompress the value with Brotli before decoding CBOR</li>
                    <li>If decompression fails, ignore the tag (inscription remains valid)</li>
                    <li>Tags without the suffix are raw CBOR — no decompression attempted</li>
                </ol>
                <h3 class="text-lg font-bold mb-3">Inscriber Behavior</h3>
                <p class="mb-2">The inscriber should:</p>
                <ol class="list-decimal ml-6 space-y-2 mb-6">
                    <li>Encode the data as CBOR</li>
                    <li>Attempt Brotli compression</li>
                    <li>Compare raw vs compressed size</li>
                    <li>Use the tag name (<code>"properties"</code> vs <code>"properties;br"</code>) that produces the smallest result</li>
                </ol>
                <h3 class="text-lg font-bold mb-3">Constraints</h3>
                <ul class="list-disc ml-6 space-y-1 mb-6">
                    <li>Maximum uncompressed size: 4,000 bytes</li>
                    <li>Maximum compression ratio: 30:1 (to prevent decompression bombs)</li>
                </ul>
            </section>

            <section id="script-sig-budget">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">ScriptSig Space Budget</h2>
                <p class="mb-4">
                    Pepecoin uses P2SH scriptSig inscriptions without SegWit. The <code>IsStandard</code> policy limits scriptSig to <strong>1650 bytes</strong>. After signature and redeem script overhead (~150 bytes), roughly <strong>1500 bytes</strong> are available for inscription payload per reveal transaction.
                </p>
                <h3 class="text-lg font-bold mb-3">Chunk packing</h3>
                <p class="mb-4">
                    Body data is split into 240-byte chunks. Each chunk encodes as 242 bytes (1-byte <code>OP_PUSHDATA1</code> prefix + 240 bytes data + 1-byte countdown number), fitting <strong>~6 chunks per reveal</strong> — approximately <strong>1,440 bytes of body data per reveal transaction</strong>.
                </p>
                <h3 class="text-lg font-bold mb-3">Practical example</h3>
                <p class="mb-4">
                    A 10 KB image requires ~42 chunks across ~7 reveal transactions. A 50 KB image needs ~35 reveals. Multi-reveal chains are the norm on Pepecoin, not the exception.
                </p>
                <p>
                    PRC-721 tags (parent, properties, metadata) are appended after the final body chunk. They add at most 1–2 extra reveals to a chain that is already many transactions long. Compression (<code>";br"</code> suffix) helps keep this overhead minimal.
                </p>
            </section>

            <section id="backwards-compatibility">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Backwards Compatibility</h2>
                <h3 class="text-lg font-bold mb-3">Practical context</h3>
                <p class="mb-4">
                    In practice, indexer divergence already exists in the Pepecoin inscription ecosystem. The <code>ordpep</code> indexer supports 520-byte push data (the maximum allowed by consensus), while older apezord-based parsers assume 240-byte chunks. Any inscription using larger pushes already produces different results across indexers. The <code>ordpep</code> indexer is currently the only actively maintained Pepecoin inscription indexer — there is no fragmented ecosystem to break.
                </p>
                <h3 class="text-lg font-bold mb-3">Old parsers reading PRC-721 inscriptions</h3>
                <p class="mb-4">
                    Old parsers (without tag support) will process the body countdown to 0 and <strong>stop reading</strong> — they never see the tag trailer. Inscription numbers remain consistent across all indexer versions.
                </p>
                <h3 class="text-lg font-bold mb-3">New parsers reading old inscriptions</h3>
                <p class="mb-4">
                    New parsers will parse the body countdown to 0 as normal, find no remaining pushes, and index the inscription with no parent/delegate metadata. 100% compatible.
                </p>
                <h3 class="text-lg font-bold mb-3">Inscription number consistency</h3>
                <p class="mb-4">
                    Inscription numbers remain consistent across old and new indexers. Old parsers never skip PRC-721 inscriptions — they just don't extract the metadata.
                </p>
            </section>

            <section id="validation-rules">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Validation Rules</h2>
                <h3 class="text-lg font-bold mb-3">Parent Validation</h3>
                <p class="mb-4">A conforming indexer MUST verify parent/child relationships cryptographically:</p>
                <ol class="list-decimal ml-6 space-y-2 mb-6">
                    <li>Parse the <code>"parent"</code> tag from the tag trailer</li>
                    <li>Check that the <strong>first reveal transaction</strong> in the child's chain has the parent inscription's UTXO as one of its inputs</li>
                    <li>Only if both conditions are met, record the parent/child relationship</li>
                </ol>
                <p class="mb-4">The tag alone is <strong>not</strong> proof of parentage — anyone can write a tag. The UTXO spend proves ownership of the parent inscription at the time the child was created.</p>

                <h3 class="text-lg font-bold mb-3">Delegate Resolution</h3>
                <p class="mb-4">When serving inscription content:</p>
                <ol class="list-decimal ml-6 space-y-2 mb-6">
                    <li>Check if the inscription has a <code>"delegate"</code> tag</li>
                    <li>If yes, look up the delegate inscription (single level — no recursive resolution)</li>
                    <li>Serve the delegate's content and content type</li>
                    <li>If the delegate does not exist, return 404</li>
                </ol>
                <p class="mb-4">Delegate chaining is not supported — the delegate target must be a content inscription, not another delegate. The indexer resolves only one level.</p>
            </section>

            <section id="parser-pseudocode">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Parser Pseudocode</h2>
                <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm md:text-base mb-4">
<pre><code>parse(sig_scripts):
  pushes = decode_all_push_data(sig_scripts)

  # Standard envelope parsing
  assert pushes[0] == "ord"
  npieces = to_number(pushes[1])
  content_type = pushes[2]

  # Body countdown
  body = []
  i = 3
  for countdown in (npieces-1)..0:
    assert to_number(pushes[i]) == countdown
    body.append(pushes[i+1])
    i += 2

  # PRC-721 tag trailer (optional)
  tags = {}
  while i + 1 &lt; len(pushes):
    key = to_string(pushes[i])
    value = pushes[i+1]
    tags[key].append(value)    # support repeated tags
    i += 2

  return Inscription { content_type, body, tags }</code></pre>
                </div>
            </section>

            <section id="extensibility">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Extensibility</h2>
                <p>
                    New tags can be added without a protocol upgrade — old parsers and old PRC-721 parsers will simply ignore unknown tags. Tag keys are UTF-8 strings, values are raw bytes with interpretation defined per tag.
                </p>
            </section>

            <section id="references">
                <h2 class="text-xl md:text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">References</h2>
                <ul class="list-disc ml-6 space-y-2">
                    <li><a href="https://github.com/mvdnbrk/ord-pepecoin" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">mvdnbrk/ord-pepecoin</a> — Ordinals indexer for Pepecoin</li>
                    <li><a href="https://github.com/ordinals/ord" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">ordinals/ord</a> — Bitcoin inscription standard (Taproot/witness)</li>
                    <li><a href="https://github.com/apezord/ord-dogecoin" class="text-green-700 dark:text-green-400 hover:underline" target="_blank">apezord/ord-dogecoin</a> — P2SH scriptSig inscription indexer, fork of ordinals/ord v0.5. Basis for Pepecoin inscription support.</li>
                    <li>PRC-20 — Pepecoin fungible token standard (same mechanics as BRC-20 on Bitcoin and DRC-20 on Dogecoin, no formal spec exists for Pepecoin)</li>
                </ul>
            </section>
        </div>
    </article>
</x-layout>
