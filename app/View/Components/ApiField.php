<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ApiField extends Component
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $description = null,
    ) {
        //
    }

    public function render(): View
    {
        return view('components.api-field');
    }
}
