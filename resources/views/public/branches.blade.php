@extends('layouts.public')

@section('title', 'Branches - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Our Branches</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Find us at a location near you. We're expanding to serve more communities.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Dar es Salaam --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                <div class="h-48 bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-emerald-900 text-lg mb-2">Mlimani City Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Mlimani City, Dar es Salaam, Tanzania</p>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> +255 678 233 736</p>
                        <p class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Mon - Sat: 7:00 AM - 8:00 PM</p>
                    </div>
                    <a href="{{ route('public.appointments') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                        Book Appointment
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- Coming Soon placeholders --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm opacity-75">
                <div class="h-48 bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                    <span class="text-white/50 font-bold text-lg">Coming Soon</span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-emerald-900 text-lg mb-2">Arusha Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Opening soon to serve the Arusha community.</p>
                </div>
            </div>
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm opacity-75">
                <div class="h-48 bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                    <span class="text-white/50 font-bold text-lg">Coming Soon</span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-emerald-900 text-lg mb-2">Mwanza Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Opening soon to serve the Mwanza community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
