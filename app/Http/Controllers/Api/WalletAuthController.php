<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletAuthChallengeRequest;
use App\Http\Requests\WalletAuthTokenRequest;
use App\Models\WalletUser;
use App\Services\PepecoinRpcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class WalletAuthController extends Controller
{
    public function __construct(
        private readonly PepecoinRpcService $rpc
    ) {}

    public function challenge(WalletAuthChallengeRequest $request): JsonResponse
    {
        $address = $request->validated('address');
        $nonce = 'peppool_wallet_auth_'.bin2hex(random_bytes(20));

        Cache::put(
            key: "wallet_auth_challenge:{$address}",
            value: $nonce,
            ttl: now()->addSeconds(90),
        );

        return response()->json([
            'nonce' => $nonce,
            'expires_in' => 90,
        ]);
    }

    public function token(WalletAuthTokenRequest $request): JsonResponse
    {
        $address = $request->validated('address');
        $signature = $request->validated('signature');

        $nonce = Cache::pull("wallet_auth_challenge:{$address}");

        if (! $nonce) {
            return response()->json(['message' => 'Challenge expired or not found.'], 422);
        }

        if (! $this->verifySignature($address, $signature, $nonce)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $walletUser = WalletUser::firstOrCreate(
            ['address' => $address],
            ['version' => $request->validated('version')],
        );

        $walletUser->update([
            'version' => $request->validated('version'),
        ]);

        $walletUser->tokens()->delete();

        $token = $walletUser->createToken(
            name: 'wallet',
            abilities: ['wallet'],
            expiresAt: now()->addDay(),
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at->timestamp,
        ]);
    }

    private function verifySignature(string $address, string $signature, string $message): bool
    {
        try {
            return $this->rpc->verifyMessage($address, $signature, $message);
        } catch (\Exception) {
            return false;
        }
    }
}
