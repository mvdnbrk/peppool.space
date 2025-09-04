<?php

declare(strict_types=1);

namespace App\Data\Rpc;

final readonly class RawTransactionVerboseData
{
    /** @param RawVinData[] $vin */
    /** @param RawVoutData[] $vout */
    public function __construct(
        public string $hex,
        public string $txid,
        public string $hash,
        public int $size,
        public int $vsize,
        public int $version,
        public int $locktime,
        public array $vin,
        public array $vout,
        public ?string $blockhash,
        public ?int $confirmations,
        public ?int $time,
        public ?int $blocktime,
    ) {}

    public static function fromRpc(array $payload): self
    {
        $vin = [];
        if (isset($payload['vin']) && is_array($payload['vin'])) {
            foreach ($payload['vin'] as $in) {
                if (is_array($in)) {
                    $vin[] = RawVinData::fromRpc($in);
                }
            }
        }

        $vout = [];
        if (isset($payload['vout']) && is_array($payload['vout'])) {
            foreach ($payload['vout'] as $out) {
                if (is_array($out)) {
                    $vout[] = RawVoutData::fromRpc($out);
                }
            }
        }

        return new self(
            hex: (string) ($payload['hex'] ?? ''),
            txid: (string) ($payload['txid'] ?? ''),
            hash: (string) ($payload['hash'] ?? ''),
            size: (int) ($payload['size'] ?? 0),
            vsize: (int) ($payload['vsize'] ?? ($payload['size'] ?? 0)),
            version: (int) ($payload['version'] ?? 0),
            locktime: (int) ($payload['locktime'] ?? 0),
            vin: $vin,
            vout: $vout,
            blockhash: isset($payload['blockhash']) ? (string) $payload['blockhash'] : null,
            confirmations: isset($payload['confirmations']) ? (int) $payload['confirmations'] : null,
            time: isset($payload['time']) ? (int) $payload['time'] : null,
            blocktime: isset($payload['blocktime']) ? (int) $payload['blocktime'] : null,
        );
    }
}
