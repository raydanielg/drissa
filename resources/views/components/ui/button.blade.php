@props([
    'variant' => 'default',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
])

@php
$base = "group/button inline-flex shrink-0 items-center justify-center rounded border border-transparent text-sm font-medium whitespace-nowrap transition-colors outline-none select-none focus-visible:ring-2 focus-visible:ring-emerald-300 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4";

$variants = [
    'default' => 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm',
    'outline' => 'border-emerald-600 bg-white text-emerald-700 hover:bg-emerald-50',
    'secondary' => 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
    'ghost' => 'text-gray-600 hover:bg-emerald-50 hover:text-emerald-700',
    'destructive' => 'bg-red-50 text-red-600 hover:bg-red-100',
    'link' => 'text-emerald-700 underline-offset-4 hover:underline',
    'emerald' => 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm',
    'gold' => 'bg-amber-500 text-white hover:bg-amber-600 shadow-sm',
];

$sizes = [
    'default' => 'h-9 gap-1.5 px-4',
    'xs' => "h-6 gap-1 px-2 text-xs [&_svg:not([class*='size-'])]:size-3",
    'sm' => 'h-8 gap-1 px-3 text-xs',
    'lg' => 'h-10 gap-1.5 px-5',
    'icon' => 'size-9',
    'icon-xs' => "size-6 [&_svg:not([class*='size-'])]:size-3",
    'icon-sm' => 'size-8',
    'icon-lg' => 'size-10',
];

$classes = trim($base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']));
@endphp

@if($href)
    <a href="{{ $href }}" data-slot="button" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" data-slot="button" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
