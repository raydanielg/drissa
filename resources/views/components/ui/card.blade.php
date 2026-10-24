@props(['size' => 'default'])

<div data-slot="card" data-size="{{ $size }}" {{ $attributes->merge(['class' => "group/card flex flex-col gap-(--card-spacing) overflow-hidden rounded-md bg-white border border-gray-200 py-(--card-spacing) text-sm text-gray-600 [--card-spacing:--spacing(5)] has-[>img:first-child]:pt-0 data-[size=sm]:[--card-spacing:--spacing(4)]"]) }}>
    {{ $slot }}
</div>
