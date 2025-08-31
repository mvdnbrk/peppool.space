<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PriceController extends Controller
{
    public function __invoke(): View
    {
        return view('price');
    }
}
