<?php

namespace App\Http\Controllers;

use App\Services\PepecoinExplorerService;

class ConverterController extends Controller
{
    public function __construct(
        private readonly PepecoinExplorerService $explorer
    ) {}

    public function index()
    {
        $prices = $this->explorer->getPrices();

        return view('converter', [
            'price' => (object) [
                'usd' => $prices->get('USD', 0),
                'eur' => $prices->get('EUR', 0),
            ],
            'title' => 'PEPE Currency Converter - Convert PEPE to USD/EUR',
            'description' => 'Convert Pepecoin (PEPE) to USD, EUR and other currencies with real-time exchange rates.',
            'og_image' => 'pepecoin-converter.png',
        ]);
    }
}
