<?php

namespace App\Providers;

use App\Services\PepecoinExplorerService;
use App\Services\PepecoinPriceService;
use App\Services\PepecoinRpcService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PepecoinRpcService::class);
        $this->app->singleton(PepecoinExplorerService::class);
        $this->app->singleton(PepecoinPriceService::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
