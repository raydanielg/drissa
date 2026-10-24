@extends('layouts.public')

@section('title', 'Shop - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Shop</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Health products and supplies available at our clinic.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                        <div class="h-40 bg-slate-100 flex items-center justify-center">
                            <i class="hgi-stroke hgi-package text-base text-slate-300"></i>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 text-sm mb-1">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->category ?? 'General' }}</p>
                            <p class="text-sm font-bold text-slate-800">TSh {{ number_format($product->selling_price) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="text-center py-20">
                <i class="hgi-stroke hgi-shopping-cart-01 text-base mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400">No products available</h3>
                <p class="text-sm text-gray-400 mt-2">Check back soon for health products and supplies.</p>
            </div>
        @endif
    </div>
</section>

@endsection
