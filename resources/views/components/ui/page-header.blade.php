@props([
    'section' => null,
    'title' => null,
    'description' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'bg-slate-800 rounded-md p-6 mb-5 text-white']) }}>
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            @if($section)
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $section }}</p>
            @endif
            <h2 class="text-xl font-bold">{{ $title }}</h2>
            @if($description)
                <p class="text-slate-400 text-xs mt-1">{{ $description }}</p>
            @endif
        </div>
        @if($slot->isNotEmpty())
            <div class="flex items-center gap-2">{{ $slot }}</div>
        @endif
    </div>
</div>
