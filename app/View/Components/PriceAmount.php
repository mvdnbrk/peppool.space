<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PriceAmount extends Component
{
    public float $value;

    public string $currencySymbol;

    public ?string $currencyCode;

    public int $precision;

    public string $dimClass;

    public string $dimSizeClass;

    // Computed parts
    public string $intPart = '0';

    public string $leadingZeros = '';

    public string $significantDecimals = '';

    public string $ariaLabel = '';

    public bool $hasSignificantDecimals = false;

    public function __construct(
        float $value = 0,
        string $currencySymbol = '$',
        ?string $currencyCode = null,
        int $precision = 8,
        string $dimClass = 'text-gray-400 dark:text-gray-500',
        string $dimSizeClass = 'text-xl',
    ) {
        $this->value = $value;
        $this->currencySymbol = $currencySymbol;
        $this->currencyCode = $currencyCode;
        $this->precision = $precision;
        $this->dimClass = $dimClass;
        $this->dimSizeClass = $dimSizeClass;

        $this->computeParts();
    }

    protected function computeParts(): void
    {
        $num = is_numeric($this->value) ? (float) $this->value : 0.0;
        $fixed = number_format($num, $this->precision, '.', '');
        [$int, $dec] = array_pad(explode('.', $fixed, 2), 2, '');
        $this->intPart = $int;

        if ($dec !== '') {
            $leading = '';
            if (preg_match('/^(0+)/', $dec, $m)) {
                $leading = $m[1];
            }
            $rest = substr($dec, strlen($leading));
            $restTrimmed = rtrim($rest, '0');

            $this->leadingZeros = $leading;
            $this->significantDecimals = $restTrimmed;
            $this->hasSignificantDecimals = ($restTrimmed !== '');
        }

        // Accessible label: currency symbol + full fixed number + optional code
        $this->ariaLabel = trim($this->currencySymbol . ' ' . $fixed . ($this->currencyCode ? ' ' . $this->currencyCode : ''));
    }

    // Note: Blade view should use $hasSignificantDecimals boolean variable directly

    public function render(): View|Closure|string
    {
        return view('components.price-amount');
    }
}
