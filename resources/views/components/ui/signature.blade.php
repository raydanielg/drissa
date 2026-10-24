@props([
    'path' => 'M126.4 164.1 C166.4 156.1 259 148.8 283 148.8 C324.3 150 299 276.7 295.5 324.1 C289.2 368.9 262.9 382.1 233.3 337.2 C213.9 310 221.2 240 360.2 244.9 C362 288.1 349.3 387.5 344.7 458.2 C344.7 458.2 348.9 289.8 382.8 245.7 C404.5 222.9 450.3 223.3 442.3 269.4 C414.9 328.6 344.9 333.4 316.2 334.3 C340.2 334.3 547.2 320.3 571.2 320.3',
    'viewBox' => '0 0 900 460',
    'size' => '100%',
    'strokeWidth' => 10,
    'duration' => 2.8,
    'delay' => 0,
    'easing' => 'cubic-bezier(0.2, 0.2, 0.8, 0.9)',
    'ariaLabel' => 'Animated signature',
    'class' => '',
])
@php
    $width = is_numeric($size) ? $size . 'px' : $size;
    $dur = is_numeric($duration) ? $duration . 's' : $duration;
    $del = is_numeric($delay) ? $delay . 's' : $delay;
@endphp

<svg aria-label="{{ $ariaLabel }}" role="img" viewBox="{{ $viewBox }}"
     class="block overflow-visible {{ $class }}" style="width: {{ $width }}; height: auto;">
    <path class="sig-stroke" d="{{ $path }}" pathLength="1"
          style="stroke-width: {{ $strokeWidth }}; animation-delay: {{ $del }}; animation-duration: {{ $dur }}; animation-timing-function: {{ $easing }};" />
</svg>

@once
@push('styles')
<style>
.sig-stroke {
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-dasharray: 1;
    stroke-dashoffset: 1;
    animation-name: sigDraw;
    animation-fill-mode: forwards;
}
@keyframes sigDraw { to { stroke-dashoffset: 0; } }
@media (prefers-reduced-motion: reduce) {
    .sig-stroke { animation-duration: 1ms !important; animation-delay: 0s !important; }
}
</style>
@endpush
@endonce
