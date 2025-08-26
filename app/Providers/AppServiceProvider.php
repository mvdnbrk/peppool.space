<?php

namespace App\Providers;

use App\Services\PepecoinRpcService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        \Blade::component('svg.pizza-ninjas-pepe', \App\View\Components\Svg\PizzaNinjasPepe::class);
    }
}
