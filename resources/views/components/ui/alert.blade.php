@props(['variant' => 'default'])

@php
$base = "group/alert relative w-full rounded-md bg-white border-l-2 p-3 text-xs text-gray-700 has-[>svg]:flex has-[>svg]:items-start has-[>svg]:gap-2 *:[svg]:shrink-0 *:[svg]:mt-px *:[svg:not([class*='size-'])]:size-3.5";

$variants = [
    'default' => 'border-slate-400',
    'destructive' => 'border-red-500 text-red-700',
    'emerald' => 'border-emerald-500',
    'gold' => 'border-amber-500',
    'info' => 'border-slate-400',
];

$classes = trim($base . ' ' . ($variants[$variant] ?? $variants['default']));
@endphp

<div data-slot="alert" role="alert" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</div>
