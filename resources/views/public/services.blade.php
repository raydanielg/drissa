@extends('layouts.public')

@section('title', 'Services - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Our Services</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Comprehensive reproductive health and family planning services tailored to your needs.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
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
                        <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                            <i class="hgi-stroke hgi-plus-sign text-2xl text-slate-600"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $service->description ?? 'Professional reproductive health service tailored to your needs.' }}</p>
                        @if($service->price)
                            <p class="text-sm font-bold text-slate-800 mb-3">TSh {{ number_format($service->price) }}</p>
                        @endif
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                            Read more
                            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $services->links() }}</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($defaultServices as $svc)
                    <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                            <i class="hgi-stroke hgi-plus-sign text-2xl text-slate-600"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $svc[0] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $svc[1] }}</p>
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                            Read more
                            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-12">
            <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-bold px-6 py-3 rounded transition-colors">
                Book an Appointment
                <i class="hgi-stroke hgi-arrow-right-01 text-xl"></i>
            </a>
        </div>
    </div>
</section>

@endsection
