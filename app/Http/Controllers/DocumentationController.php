<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocumentationController extends Controller
{
    public function __invoke(): View
    {
        return view('docs.api');
    }
}
