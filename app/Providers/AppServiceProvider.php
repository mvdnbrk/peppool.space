<?php

namespace App\Providers;

use App\Contracts\BlockchainServiceInterface;
use App\Models\WalletUser;
use App\Services\BlockchainService;
use App\Services\ElectrsPepeService;
use App\Services\OrdinalsService;
use App\Services\PepecoinExplorerService;
use App\Services\PepecoinPriceService;
use App\Services\PepecoinRpcService;
use App\Services\RpcBlockchainService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PepecoinRpcService::class);
        $this->app->singleton(ElectrsPepeService::class);
        $this->app->singleton(RpcBlockchainService::class);
        $this->app->singleton(BlockchainService::class);
        $this->app->singleton(PepecoinExplorerService::class);
        $this->app->singleton(PepecoinPriceService::class);
        $this->app->singleton(OrdinalsService::class);

        $this->app->bind(BlockchainServiceInterface::class, BlockchainService::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();

        RateLimiter::for('challenge', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            if ($request->hasSession() && $request->session()->has('_token')) {
                return Limit::none();
            }

            $user = auth('sanctum')->user();

            if ($user instanceof WalletUser) {
                return Limit::perMinute(60)->by('peppool_wallet:'.$user->id);
            }

            return Limit::perMinute(15)->by($request->ip());
        });
    }
}
