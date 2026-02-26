<?php
$rpc = app(App\Services\PepecoinRpcService::class);
$tip = $rpc->getBlockCount();
$results = [];

for ($i = 0; $i < 10; $i++) {
    $height = $tip - $i;
    $hash = $rpc->getBlockHash($height);
    $block = $rpc->getBlock($hash, 2);
    $coinbase = $block['tx'][0] ?? null;

    if (!$coinbase) continue;

    $vin = $coinbase['vin'][0] ?? null;
    $scriptsig = $vin['coinbase'] ?? ''; // In getblock verbosity 2, it's 'coinbase' for coinbase txs
    
    $decoded = '';
    if (!empty($scriptsig)) {
        try {
            $binary = @hex2bin($scriptsig);
            if ($binary) {
                $decoded = preg_replace('/[[:^print:]]/', ' ', $binary);
            }
        } catch (\Exception $e) {}
    }

    $results[] = [
        'h' => $height,
        'addr' => $coinbase['vout'][0]['scriptPubKey']['addresses'][0] ?? '?',
        'tag' => trim($decoded),
    ];
}

foreach ($results as $r) {
    echo "Height: {$r['h']} | Address: {$r['addr']} | Tag: {$r['tag']}\n";
}
