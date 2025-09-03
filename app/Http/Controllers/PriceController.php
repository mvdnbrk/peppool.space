<?php

namespace App\Http\Controllers;

use App\Services\PepecoinExplorerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function __construct(private readonly PepecoinExplorerService $explorer) {}

    public function __invoke(Request $request): View
    {
        $currency = strtoupper($request->get('currency', 'USD'));
        $prices = $this->explorer->getPrices();
        $price = (float) ($prices->get($currency) ?? 0);
        // Only show supply if it's already cached; do not compute here
        $supply = Cache::get('pepe:total_supply');
        $marketCap = $this->explorer->getMarketCap($currency);

        return view('price', [
            'currency' => $currency,
            'price' => $price,
            'supply' => $supply, // string|null
            'marketCap' => $marketCap, // float|null
        ]);
    }
}
