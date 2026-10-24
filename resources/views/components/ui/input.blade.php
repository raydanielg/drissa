@props(['type' => 'text'])

<input type="{{ $type }}" data-slot="input" {{ $attributes->merge(['class' => 'h-9 w-full min-w-0 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm text-gray-900 transition-shadow outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-400 focus-visible:ring-2 focus-visible:ring-slate-300 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-red-300 aria-invalid:ring-red-100']) }} />
