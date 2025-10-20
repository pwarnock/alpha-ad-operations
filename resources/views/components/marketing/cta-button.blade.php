@props([
    'href' => '#',
    'text' => 'Get Started',
    'variant' => 'primary',
    'size' => 'lg'
])

@php
$baseClasses = 'inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md transition-all duration-300 cta-button';

$variantClasses = match($variant) {
    'primary' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'text-blue-700 bg-blue-100 hover:bg-blue-200 focus:ring-blue-500',
    'outline' => 'text-blue-700 bg-transparent border-blue-600 hover:bg-blue-50 focus:ring-blue-500',
    default => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
};

$sizeClasses = match($size) {
    'sm' => 'px-4 py-2 text-sm',
    'lg' => 'px-8 py-4 text-lg',
    'xl' => 'px-10 py-5 text-xl',
    default => 'px-6 py-3 text-base',
};

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;
@endphp

<a href="{{ $href }}" class="{{ $classes }}">
    {{ $text }}
</a>