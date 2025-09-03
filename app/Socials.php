<?php

namespace App;

use Illuminate\Support\Str;

class Socials
{
    public string $twitter_url;

    public string $telegram_url;

    public string $discord_url;

    public string $youtube_url;

    public string $reddit_url;

    public string $facebook_url;

    public string $tiktok_url;

    public string $instagram_url;

    public string $github_url;

    public function __construct()
    {
        $this->twitter_url = Str::of('https://x.com/')->append(
            config('pepecoin.socials.twitter_handle')
        );

        $this->telegram_url = Str::of('https://t.me/')->append(
            config('pepecoin.socials.telegram_handle')
        );

        $this->discord_url = Str::of('https://discord.com/invite/')->append(
            config('pepecoin.socials.discord_handle')
        );

        $this->youtube_url = Str::of('https://www.youtube.com/@')->append(
            config('pepecoin.socials.youtube_handle')
        );

        $this->reddit_url = Str::of('https://www.reddit.com/')->append(
            config('pepecoin.socials.reddit_handle')
        );

        $this->facebook_url = Str::of('https://www.facebook.com/')->append(
            config('pepecoin.socials.facebook_handle')
        );

        $this->tiktok_url = Str::of('https://www.tiktok.com/')->append(
            config('pepecoin.socials.tiktok_handle')
        );

        $this->instagram_url = Str::of('https://www.instagram.com/')->append(
            config('pepecoin.socials.instagram_handle')
        );

        $this->github_url = Str::of('https://github.com/')->append(
            config('pepecoin.socials.github_handle')
        );
    }
}
