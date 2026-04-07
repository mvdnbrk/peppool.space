<?php

namespace Tests\Feature;

use App\Models\WalletUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RejectInvalidBearerTokenTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function request_without_token_proceeds_normally(): void
    {
        $response = $this->getJson(route('api.prices'));

        $response->assertOk();
    }

    #[Test]
    public function request_with_valid_token_proceeds_normally(): void
    {
        $user = WalletUser::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.prices'));

        $response->assertOk();
    }

    #[Test]
    public function request_with_invalid_token_returns_401(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-here',
        ])->getJson(route('api.prices'));

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Invalid or expired token.']);
    }

    #[Test]
    public function request_with_expired_token_returns_401(): void
    {
        $user = WalletUser::factory()->create();
        $token = $user->createToken('test');
        $token->accessToken->update(['expires_at' => now()->subDay()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ])->getJson(route('api.prices'));

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Invalid or expired token.']);
    }
}
