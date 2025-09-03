<?php

namespace App\Http\Controllers;

use App\Socials;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $socials = new Socials;

        return view('about', compact('socials'));
    }
}
