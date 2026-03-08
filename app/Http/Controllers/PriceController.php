<?php

namespace App\Http\Controllers;

use App\Services\PepecoinPriceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function __construct(private readonly PepecoinPriceService $pricesService) {}

    public function __invoke(Request $request): View
    {
        $currency = strtoupper($request->get('currency', 'USD'));
        $prices = $this->pricesService->getPrices();
        $price = (float) ($prices->get($currency) ?? 0);
        $supply = $this->pricesService->getTotalSupply() ?: null;
        $marketCap = $this->pricesService->getMarketCap($currency) ?: null;

        return view('price', [
            'currency' => $currency,
            'price' => $price,
            'supply' => $supply, // string|null
            'marketCap' => $marketCap, // float|null
        ]);
    }
}
