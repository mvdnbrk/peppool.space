@props([
    'color' => 'text-green-500',
    'height' => 'h-2',
    'flip' => false,
    'class' => ''
])

<div class="w-full {{ $height }} overflow-hidden {{ $class }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" class="w-full h-full {{ $flip ? 'transform scale-x-[-1]' : '' }} {{ $color }}">
        <path fill="currentColor" d="M0,224L48,224C96,224,192,224,288,197.3C384,171,480,117,576,117.3C672,117,768,171,864,202.7C960,235,1056,245,1152,234.7C1248,224,1344,192,1392,176L1440,160L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
    </svg>
</div>
