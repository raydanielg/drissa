@extends('layouts.public')

@section('title', 'Services - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Our Services</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Comprehensive reproductive health and family planning services tailored to your needs.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $defaultServices = [
                ['Family Planning Counseling', 'Expert guidance on contraceptive methods and family planning options tailored to your reproductive goals and health needs.'],
                ['Maternal & Reproductive Health', 'Comprehensive maternal care and reproductive health consultations from preconception to postpartum support.'],
                ['Pregnancy Testing & Prenatal Care', 'Reliable pregnancy testing and thorough prenatal care support to ensure a healthy pregnancy journey.'],
                ['Reproductive Health Education', 'Empowering individuals and couples with knowledge about reproductive health, fertility, and informed decision-making.'],
                ['Confidential Counseling', 'Private and supportive counseling sessions for reproductive health concerns with complete confidentiality.'],
                ['General Consultation', 'Professional medical consultations for all your reproductive health concerns in a safe, welcoming environment.'],
            ];
        @endphp

        @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <h3 class="font-bold text-emerald-900 text-lg mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $service->description ?? 'Professional reproductive health service tailored to your needs.' }}</p>
                        @if($service->price)
                            <p class="text-sm font-bold text-gold-600 mb-3">TSh {{ number_format($service->price) }}</p>
                        @endif
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                            Read more
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $services->links() }}</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($defaultServices as $svc)
                    <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <h3 class="font-bold text-emerald-900 text-lg mb-2">{{ $svc[0] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $svc[1] }}</p>
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                            Read more
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-12">
            <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-lg shadow-sm hover:shadow-md transition-all">
                Book an Appointment
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

@endsection
