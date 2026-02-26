<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MiningPoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pools = [
            [
                'name' => 'Unknown',
                'link' => null,
                'addresses' => [],
                'regexes' => [],
            ],
            [
                'name' => 'ViaBTC',
                'link' => 'https://www.viabtc.com/',
                'addresses' => ['Pd4Ee73nvvymgiGKMouC1GYZpQCrgMmCjd'],
                'regexes' => ['ViaBTC'],
            ],
            [
                'name' => 'F2Pool',
                'link' => 'https://www.f2pool.com/',
                'addresses' => ['PsHArZKD6oodN47QW5oqaTXTw27jEdDyiW', 'PtLNDTmSvauqJfdnnFELAZmRMnVg87kFTH'],
                'regexes' => ['F2Pool', '🐟'],
            ],
            [
                'name' => 'AntPool',
                'link' => 'https://www.antpool.com/',
                'addresses' => ['PjdviQbYuVu4RgX5N3SVebpcTHyuaZq8uj', 'Pq5FyUWjK4sNH83v3ppib8NHgePMUrAEhd'],
                'regexes' => ['AntPool'],
            ],
            [
                'name' => 'KuPool',
                'link' => 'https://www.kucoin.com/mining-pool',
                'addresses' => ['PbwHwFpGaDYRejHKRqvsrmyKH1QWyMEzPD'],
                'regexes' => ['kupool', 'KuCoinPool'],
            ],
            [
                'name' => '00Hash',
                'link' => 'https://00hash.com/',
                'addresses' => ['Pfz1FHLRYAW8NNc6N6n63t2DJYJrCNsLo9'],
                'regexes' => ['00Hash'],
            ],
            [
                'name' => 'Binance Pool',
                'link' => 'https://pool.binance.com/',
                'addresses' => ['PmAKzVn2hqR6a2whuzkD7jLV4Q5ALSctSK'],
                'regexes' => ['Binance'],
            ],
            [
                'name' => 'EMCD',
                'link' => 'https://emcd.io/',
                'addresses' => ['PvSYktoNfDa2BRU5q39Ldafpx6Ei2goJLr'],
                'regexes' => ['emcd', 'one_more_mcd'],
            ],
            [
                'name' => 'Mining-Dutch',
                'link' => 'https://www.mining-dutch.nl/',
                'addresses' => ['PhHpo2HmqWZnBEsmFeQYLHHKxTeF25yfX8'],
                'regexes' => ['Mining-Dutch'],
            ],
            [
                'name' => 'TrustPool',
                'link' => 'https://trustpool.cc/',
                'addresses' => [],
                'regexes' => ['TrustPool'],
            ],
            [
                'name' => 'LitecoinPool',
                'link' => 'https://www.litecoinpool.org/',
                'addresses' => [],
                'regexes' => ['LitecoinPool'],
            ],
            [
                'name' => 'CloverPool',
                'link' => 'https://cloverpool.com/',
                'addresses' => [],
                'regexes' => ['CloverPool'],
            ],
        ];

        foreach ($pools as $pool) {
            Pool::updateOrCreate(
                ['slug' => Str::slug($pool['name'])],
                [
                    'name' => $pool['name'],
                    'link' => $pool['link'],
                    'addresses' => $pool['addresses'],
                    'regexes' => $pool['regexes'],
                ]
            );
        }
    }
}
