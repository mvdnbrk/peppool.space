<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pepecoin RPC Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the Pepecoin node via RPC.
    | The connection is established through an SSH tunnel as described
    | in the project documentation.
    |
    */

    'rpc' => [
        'host' => env('PEPECOIN_RPC_HOST', '127.0.0.1'),
        'port' => env('PEPECOIN_RPC_PORT', '33873'),
        'username' => env('PEPECOIN_RPC_USER', ''),
        'password' => env('PEPECOIN_RPC_PASSWORD', ''),
        'timeout' => env('PEPECOIN_RPC_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chain Configuration
    |--------------------------------------------------------------------------
    |
    | Pepecoin-specific chain parameters and constants.
    |
    */

    'chain' => [
        'name' => 'main',
        'default_rpc_port' => 33873,
        'default_p2p_port' => 33874,
        'address_prefix' => 'P',
        'auxpow_start_block' => 42000,
        'bip_start_block' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Explorer Configuration
    |--------------------------------------------------------------------------
    |
    | Settings specific to the blockchain explorer functionality.
    |
    */

    'explorer' => [
        'blocks_per_page' => 25,
        'transactions_per_page' => 25,
        'mempool_refresh_interval' => 5, // seconds
        'fee_estimation_blocks' => [1, 3, 6, 12], // target confirmation blocks
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | CDN base URL used by the cdn_asset() helper. When null, the helper will
    | derive the base as https://cdn.<host>[/basePath] from app.url/request.
    |
    */

    'cdn' => [
        'url' => env('CDN_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Configuration
    |--------------------------------------------------------------------------
    |
    | Social media handles and links for the Pepecoin community.
    |
    */

    'socials' => [
        'twitter_handle' => 'PepecoinNetwork',
        'telegram_handle' => 'PepecoinGroup',
        'discord_handle' => '6NXJt25q2J',
        'youtube_handle' => 'pepecoin',
        'reddit_handle' => 'r/pepecoin',
        'facebook_handle' => 'people/Pepecoin/61559208990076',
        'tiktok_handle' => '@pepecoin_',
        'instagram_handle' => 'pepecoin_pepe',
        'github_handle' => 'pepecoinppc/pepecoin',
    ],
];
