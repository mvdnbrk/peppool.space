<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function api(): View
    {
        return view('docs.api');
    }

    public function prc721(): View
    {
        return view('docs.prc-721');
    }
}
