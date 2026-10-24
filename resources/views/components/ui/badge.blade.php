@props(['variant' => 'default'])

@php
$base = "group/badge inline-flex w-fit shrink-0 items-center justify-center gap-1.5 rounded border border-transparent px-2 py-0.5 text-[10px] font-semibold whitespace-nowrap [&>svg]:pointer-events-none [&>svg]:size-3!";

$variants = [
    'default' => 'bg-gray-100 text-gray-700',
    'secondary' => 'bg-gray-100 text-gray-600',
    'destructive' => 'bg-red-50 text-red-600',
    'outline' => 'border-gray-200 bg-white text-gray-600',
    'ghost' => 'text-gray-500',
    'link' => 'text-slate-700 underline-offset-4 hover:underline',
    'emerald' => 'bg-emerald-50 text-emerald-700',
    'gold' => 'bg-amber-50 text-amber-700',
    'success' => 'bg-emerald-50 text-emerald-700',
];

$classes = trim($base . ' ' . ($variants[$variant] ?? $variants['default']));
@endphp

<span data-slot="badge" data-variant="{{ $variant }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
