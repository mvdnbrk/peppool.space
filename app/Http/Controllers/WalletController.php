<?php

namespace App\Http\Controllers;

use App\Socials;
use Illuminate\View\View;

class WalletController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $socials = new Socials;

        return view('wallet', compact('socials'));
    }
}
