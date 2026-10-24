<div data-slot="card-header" {{ $attributes->merge(['class' => "group/card-header grid auto-rows-min items-start gap-1 px-(--card-spacing) has-data-[slot=card-action]:grid-cols-[1fr_auto] has-data-[slot=card-description]:grid-rows-[auto_auto] [.border-b]:pb-(--card-spacing) [.border-b]:border-gray-100"]) }}>
    {{ $slot }}
</div>
