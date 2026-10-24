@props(['orientation' => 'horizontal'])

@if($orientation === 'vertical')
    <div data-slot="separator" role="separator" aria-orientation="vertical" {{ $attributes->merge(['class' => 'shrink-0 bg-gray-200 w-px self-stretch']) }}></div>
@else
    <div data-slot="separator" role="separator" aria-orientation="horizontal" {{ $attributes->merge(['class' => 'shrink-0 bg-gray-200 h-px w-full']) }}></div>
@endif
