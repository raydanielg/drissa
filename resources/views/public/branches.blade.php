@extends('layouts.public')

@section('title', 'Branches - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Our Branches</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Find us at a location near you. We're expanding to serve more communities.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Coming Soon placeholders --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                <div class="h-48 bg-slate-700 flex items-center justify-center">
                    <i class="hgi-stroke hgi-building-03 text-base text-white/30"></i>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">Mlimani City Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Mlimani City, Dar es Salaam, Tanzania</p>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p class="flex items-center gap-2"><i class="hgi-stroke hgi-call-02 text-base text-slate-500"></i> +255 678 233 736</p>
                        <p class="flex items-center gap-2"><i class="hgi-stroke hgi-clock-01 text-base text-slate-500"></i> Mon - Sat: 7:00 AM - 8:00 PM</p>
                    </div>
                    <a href="{{ route('public.appointments') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                        Book Appointment
                        <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                    </a>
                </div>
            </div>

            {{-- Coming Soon placeholders --}}
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm opacity-75">
                <div class="h-48 bg-slate-400 flex items-center justify-center">
                    <span class="text-white/50 font-bold text-lg">Coming Soon</span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">Arusha Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Opening soon to serve the Arusha community.</p>
                </div>
            </div>
            <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm opacity-75">
                <div class="h-48 bg-slate-400 flex items-center justify-center">
                    <span class="text-white/50 font-bold text-lg">Coming Soon</span>
                </div>
                <div class="p-6">
                    <h3 class="font-bold text-slate-900 text-lg mb-2">Mwanza Branch</h3>
                    <p class="text-sm text-gray-600 mb-4">Opening soon to serve the Mwanza community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
