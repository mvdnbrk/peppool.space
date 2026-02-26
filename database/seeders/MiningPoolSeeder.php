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
                'regexes' => ['/ViaBTC/i'],
            ],
            [
                'name' => 'F2Pool',
                'link' => 'https://www.f2pool.com/',
                'addresses' => ['PsHArZKD6oodN47QW5oqaTXTw27jEdDyiW'],
                'regexes' => ['/F2Pool/i'],
            ],
            [
                'name' => 'AntPool',
                'link' => 'https://www.antpool.com/',
                'addresses' => ['PjdviQbYuVu4RgX5N3SVebpcTHyuaZq8uj'],
                'regexes' => ['/AntPool/i'],
            ],
            [
                'name' => 'KuPool',
                'link' => 'https://www.kucoin.com/mining-pool',
                'addresses' => ['PbwHwFpGaDYRejHKRqvsrmyKH1QWyMEzPD'],
                'regexes' => ['/kupool/i'],
            ],
            [
                'name' => '00Hash',
                'link' => 'https://00hash.com/',
                'addresses' => ['Pfz1FHLRYAW8NNc6N6n63t2DJYJrCNsLo9'],
                'regexes' => ['/00Hash/i'],
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
