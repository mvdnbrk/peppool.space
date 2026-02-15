<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SponsorController extends Controller
{
    public function __invoke(): View
    {
        return view('sponsor', [
            'digitalOceanUrl' => config('services.digitalocean.referral_url'),
            'fathomUrl' => config('services.fathom.affiliate_url'),
        ]);
    }
}
