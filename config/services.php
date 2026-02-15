<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'fathom' => [
        'site_id' => env('FATHOM_SITE_ID'),
        'affiliate_url' => env('FATHOM_AFFILIATE_URL', 'https://usefathom.com/ref/FI15PB'),
    ],

    'digitalocean' => [
        'referral_url' => env('DIGITALOCEAN_REFERRAL_URL', 'https://m.do.co/c/7a24c68b1e6d'),
    ],

    'coingecko' => [
        'base_url' => 'https://api.coingecko.com/api/v3/',
    ],
];
