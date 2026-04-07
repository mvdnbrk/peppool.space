<?php

namespace Tests\Feature\Api;

use App\Models\WalletUser;
use App\Services\PepecoinRpcService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WalletAuthTest extends TestCase
{
    use RefreshDatabase;

    private function challengeRequest(array $data, string $version = '0.1.0'): TestResponse
    {
        return $this->withHeaders([
            'X-App-Name' => 'peppool-wallet',
            'X-App-Version' => $version,
        ])->postJson(route('api.auth.challenge'), $data);
    }

    private function tokenRequest(array $data, string $version = '0.1.0'): TestResponse
    {
        return $this->withHeaders([
            'X-App-Name' => 'peppool-wallet',
            'X-App-Version' => $version,
        ])->postJson(route('api.auth.token'), $data);
    }

    #[Test]
    public function challenge_returns_nonce(): void
    {
        $response = $this->challengeRequest([
            'address' => 'PTestAddress123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['nonce', 'expires_in']);
        $response->assertJson(['expires_in' => 90]);

        $this->assertNotNull(Cache::get('wallet_auth_challenge:PTestAddress123'));
    }

    #[Test]
    public function challenge_requires_address(): void
    {
        $response = $this->challengeRequest([]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['address']);
    }

    #[Test]
    public function token_fails_without_challenge(): void
    {
        $response = $this->tokenRequest([
            'address' => 'PTestAddress123',
            'signature' => 'ZmFrZXNpZw==',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Challenge expired or not found.']);
    }

    #[Test]
    public function token_fails_with_invalid_signature(): void
    {
        Cache::put('wallet_auth_challenge:PTestAddress123', 'test-nonce', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PTestAddress123', 'YmFkc2ln', 'test-nonce')
            ->andReturn(false);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $response = $this->tokenRequest([
            'address' => 'PTestAddress123',
            'signature' => 'YmFkc2ln',
        ]);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Invalid signature.']);
    }

    #[Test]
    public function token_issued_with_valid_signature(): void
    {
        Cache::put('wallet_auth_challenge:PTestAddress123', 'test-nonce', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PTestAddress123', 'dmFsaWRzaWc=', 'test-nonce')
            ->andReturn(true);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $response = $this->tokenRequest([
            'address' => 'PTestAddress123',
            'signature' => 'dmFsaWRzaWc=',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['token', 'expires_at']);

        $this->assertDatabaseHas('wallet_users', [
            'address' => 'PTestAddress123',
            'version' => '0.1.0',
        ]);
    }

    #[Test]
    public function token_creates_wallet_user_on_first_auth(): void
    {
        Cache::put('wallet_auth_challenge:PNewAddress', 'nonce1', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PNewAddress', 'c2lnMQ==', 'nonce1')
            ->andReturn(true);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $this->assertDatabaseMissing('wallet_users', ['address' => 'PNewAddress']);

        $this->tokenRequest([
            'address' => 'PNewAddress',
            'signature' => 'c2lnMQ==',
        ]);

        $this->assertDatabaseHas('wallet_users', ['address' => 'PNewAddress']);
    }

    #[Test]
    public function token_reuses_existing_wallet_user(): void
    {
        $walletUser = WalletUser::factory()->create(['address' => 'PExisting']);

        Cache::put('wallet_auth_challenge:PExisting', 'nonce2', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PExisting', 'c2lnMg==', 'nonce2')
            ->andReturn(true);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $this->tokenRequest([
            'address' => 'PExisting',
            'signature' => 'c2lnMg==',
        ]);

        $this->assertDatabaseCount('wallet_users', 1);
    }

    #[Test]
    public function challenge_nonce_is_consumed_after_token_exchange(): void
    {
        Cache::put('wallet_auth_challenge:PTestAddress123', 'one-time-nonce', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PTestAddress123', 'dmFsaWRzaWc=', 'one-time-nonce')
            ->andReturn(true);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $this->tokenRequest([
            'address' => 'PTestAddress123',
            'signature' => 'dmFsaWRzaWc=',
        ]);

        $this->assertNull(Cache::get('wallet_auth_challenge:PTestAddress123'));
    }

    #[Test]
    public function wallet_user_gets_higher_rate_limit(): void
    {
        $walletUser = WalletUser::factory()->create();

        Sanctum::actingAs($walletUser, ['wallet']);

        $response = $this->getJson(route('api.prices'));

        $response->assertOk();
        $response->assertHeader('X-RateLimit-Limit', 60);
    }

    #[Test]
    public function anonymous_requests_get_default_rate_limit(): void
    {
        $response = $this->getJson(route('api.prices'));

        $response->assertHeader('X-RateLimit-Limit', 15);
    }

    #[Test]
    public function new_token_revokes_previous_tokens(): void
    {
        $walletUser = WalletUser::factory()->create(['address' => 'PExisting']);
        $walletUser->createToken('wallet', ['wallet'], now()->addDay());
        $walletUser->createToken('wallet', ['wallet'], now()->addDay());

        $this->assertCount(2, $walletUser->tokens);

        Cache::put('wallet_auth_challenge:PExisting', 'nonce-revoke', 90);

        $rpc = Mockery::mock(PepecoinRpcService::class);
        $rpc->shouldReceive('verifyMessage')
            ->with('PExisting', 'cmV2b2tl', 'nonce-revoke')
            ->andReturn(true);
        $this->app->instance(PepecoinRpcService::class, $rpc);

        $this->tokenRequest([
            'address' => 'PExisting',
            'signature' => 'cmV2b2tl',
        ]);

        $this->assertCount(1, $walletUser->fresh()->tokens);
    }

    #[Test]
    public function challenge_endpoint_is_rate_limited(): void
    {
        RateLimiter::for('challenge', fn () => Limit::perMinute(1));

        $this->challengeRequest(['address' => 'PAddr1']);
        $response = $this->challengeRequest(['address' => 'PAddr2']);

        $response->assertStatus(429);
    }
}
