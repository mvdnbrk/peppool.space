<?php

namespace App\Providers;

use App\Services\PepecoinRpcService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PepecoinRpcService::class, function ($app) {
            return new PepecoinRpcService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
