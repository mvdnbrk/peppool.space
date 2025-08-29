<?php

namespace App\Providers;

use App\Services\PepecoinRpcService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PepecoinRpcService::class, function ($app) {
            return new PepecoinRpcService;
        });
    }

    public function boot(): void
    {
        //
    }
}
