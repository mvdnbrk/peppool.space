<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaitlistEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        WaitlistEntry::firstOrCreate(
            ['email' => $request->email],
            ['ip_address' => $request->ip()]
        );

        return response()->json([
            'message' => 'Successfully joined the waitlist.',
        ]);
    }
}
