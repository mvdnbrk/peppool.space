<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class WealthController extends Controller
{
    public function __invoke(): View
    {
        $topAddresses = DB::table('address_balances')
            ->select(['address', 'balance'])
            ->orderByDesc('balance')
            ->limit(50)
            ->get();

        return view('wealth', [
            'addresses' => $topAddresses,
        ]);
    }
}
