<?php

namespace App\Providers;

use App\Services\PepecoinRpcService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::component('views.components.svg.pizza-ninjas-pepe', \App\View\Components\Svg\PizzaNinjasPepe::class);
    }
}
