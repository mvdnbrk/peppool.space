<?php

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
