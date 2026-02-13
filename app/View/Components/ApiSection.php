<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class ApiSection extends Component
{
    public string $badgeClasses;

    public string $sectionId;

    public function __construct(
        public string $method = 'GET',
        public string $path = '/',
        public ?string $description = null,
        public ?string $id = null,
        public ?string $responseContentType = null,
    ) {
        $this->method = strtoupper($method);

        $this->badgeClasses = Collection::make([
            'GET' => 'bg-green-100 text-green-800',
            'POST' => 'bg-blue-100 text-blue-800',
            'PUT' => 'bg-yellow-100 text-yellow-800',
            'PATCH' => 'bg-yellow-100 text-yellow-800',
            'DELETE' => 'bg-red-100 text-red-800',
        ])
            ->get($this->method, 'bg-gray-100 text-gray-800');

        $this->sectionId = $id ?? Str::of($path)
            ->replace(['/', '{', '}', ':', ' ', '[', ']'], '-')
            ->trim('-')
            ->lower()
            ->value();
    }

    public function sectionId(): string
    {
        return $this->sectionId;
    }

    public function badgeClasses(): string
    {
        return $this->badgeClasses;
    }

    public function render(): View
    {
        return view('components.api-section');
    }
}
