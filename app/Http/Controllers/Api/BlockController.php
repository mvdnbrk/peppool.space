<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PepecoinExplorerService;
use Illuminate\Http\Response;

class BlockController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorerService
    ) {}

    public function tipHeight(): Response
    {
        return new Response(
            content: (string) $this->explorerService->getBlockTipHeight(),
            headers: ['Content-Type' => 'text/plain']
        );
    }

    public function tipHash(): Response
    {
        return new Response(
            content: $this->explorerService->getBlockTipHash(),
            headers: ['Content-Type' => 'text/plain']
        );
    }
}
