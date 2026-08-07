@extends('layouts.public')

@section('title', 'About Us - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">About Us</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Learn more about Uzazi Clinic and our commitment to reproductive health.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <img src="{{ asset('1411.jpg') }}" alt="Our Clinic" class="rounded-2xl shadow-xl w-full h-auto object-cover">
            </div>
            <div>
                <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Who We Are</span>
                <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Trusted Reproductive Health Clinic</h2>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Uzazi Clinic is a specialized reproductive health and family planning clinic delivering confidential, compassionate, and quality care. We are dedicated to empowering informed choices for individuals and families across Tanzania.
                </p>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Our team of experienced healthcare professionals provides a full range of reproductive health services in a safe, welcoming, and confidential environment. From family planning counseling to maternal care, we are here for you at every stage.
                </p>
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50 rounded-xl p-4">
                        <p class="text-3xl font-extrabold text-emerald-700">100%</p>
                        <p class="text-sm text-gray-600">Confidential Care</p>
                    </div>
                    <div class="bg-gold-50 rounded-xl p-4">
                        <p class="text-3xl font-extrabold text-gold-600">24/7</p>
                        <p class="text-sm text-gray-600">Support Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Our Mission & Vision</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Driven by Compassion, Guided by Science</h2>
            <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-xl mb-2">Our Mission</h3>
                <p class="text-gray-600 leading-relaxed">To provide accessible, confidential, and compassionate reproductive health services that empower individuals and families to make informed choices about their reproductive wellbeing.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-xl mb-2">Our Vision</h3>
                <p class="text-gray-600 leading-relaxed">To be the leading reproductive health clinic in Tanzania, recognized for excellence in patient care, confidentiality, and empowering communities through education and quality services.</p>
            </div>
        </div>
    </div>
</section>

@if($doctors->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Our Team</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Meet Our Expert Medical Team</h2>
            <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($doctors as $doctor)
                <div class="card-hover bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center">
                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-extrabold text-2xl mb-4">
                        {{ strtoupper(substr($doctor->name, 0, 1)) }}
                    </div>
                    <h3 class="font-bold text-emerald-900">{{ $doctor->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Medical Professional</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
