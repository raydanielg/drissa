@extends('layouts.public')

@section('title', 'Shop - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Shop</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Health products and supplies available at our clinic.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                        <div class="h-40 bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-emerald-900 text-sm mb-1">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->category ?? 'General' }}</p>
                            <p class="text-sm font-bold text-gold-600">TSh {{ number_format($product->selling_price) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="text-center py-20">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h3 class="text-xl font-bold text-gray-400">No products available</h3>
                <p class="text-sm text-gray-400 mt-2">Check back soon for health products and supplies.</p>
            </div>
        @endif
    </div>
</section>

@endsection
