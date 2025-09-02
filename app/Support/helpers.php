<?php

use Illuminate\Support\Str;
use Illuminate\Support\Number;

if (! function_exists('format_hashrate')) {
    function format_hashrate(float $hashrate): string
    {
        $units = ['H/s', 'KH/s', 'MH/s', 'GH/s', 'TH/s', 'PH/s', 'EH/s'];
        $i = 0;

        while ($hashrate >= 1000 && $i < count($units) - 1) {
            $hashrate /= 1000;
            $i++;
        }

        return Number::format($hashrate, 2).' '.$units[$i];
    }
}

if (! function_exists('format_difficulty')) {
    function format_difficulty(float $difficulty): string
    {
        $units = ['', 'K', 'M', 'G', 'T', 'P', 'E'];
        $i = 0;

        while ($difficulty >= 1000 && $i < count($units) - 1) {
            $difficulty /= 1000;
            $i++;
        }

        return Number::format($difficulty, 2).' '.$units[$i];
    }
}

if (! function_exists('cdn_asset')) {
    function cdn_asset(string $path, ?bool $secure = null): string
    {
        // If already an absolute or protocol-relative URL, return as-is
        if (preg_match('#^(//|https?://)#i', $path)) {
            return $path;
        }

        $appUrl = (string) config('app.url', '');
        $parsed = $appUrl !== '' ? parse_url($appUrl) : [];

        $host = $parsed['host'] ?? request()->getHost();
        $basePath = rtrim($parsed['path'] ?? '', '/');

        // HTTPS by default per project policy
        $scheme = $secure === false ? 'http' : 'https';

        // Prefer config-driven root (trimmed), ensure scheme when missing
        $root = Str::of(config('pepecoin.cdn.url', ''))
            ->trim()
            ->whenStartsWith(['http://', 'https://'], fn ($s) => $s, fn ($s) => $s->whenNotEmpty(fn ($x) => $x->prepend($scheme.'://')))
            ->ltrim('/');

        if ($root->isEmpty()) {
            // Derive from current/app host
            if (! $host) {
                return asset($path, $secure);
            }

            $cdnHost = Str::of($host)
                ->whenStartsWith('cdn.', fn ($s) => $s, fn ($s) => $s->prepend('cdn.'));

            $root = Str::of($scheme.'://')
                ->append($cdnHost)
                ->append($basePath);
        }

        // Delegate to Laravel's URL generator for clean URL building
        return app('url')->assetFrom((string) $root, $path, $secure);
    }
}
