@extends('layouts.public')

@section('title', 'Home - ' . config('app.name'))

@section('content')

{{-- Hero Section --}}
<section class="relative min-h-[600px] flex items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('7678.jpg') }}" alt="Clinic" class="w-full h-full object-cover">
        <div class="absolute inset-0 hero-gradient"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 bg-gold-500/20 text-gold-300 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Professional Medical Care
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                Your Reproductive Health,<br>Our Priority
            </h1>
            <p class="mt-6 text-lg text-emerald-100/90 max-w-xl leading-relaxed">
                Specialized reproductive health and family planning services — confidential, compassionate, and designed to empower your informed choices.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('public.appointments') }}" class="inline-flex items-center justify-center gap-2 bg-gold-500 hover:bg-gold-600 text-white font-bold px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Book Appointment
                </a>
                <a href="{{ route('public.services') }}" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold px-6 py-3 rounded-lg hover:bg-white/20 transition-all">
                    Our Services
                </a>
            </div>
            <div class="mt-8 flex items-center gap-4 text-white">
                <div class="flex items-center gap-2">
                    <div class="w-12 h-12 rounded-full bg-red-500/20 border border-red-400/40 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-200/70 uppercase tracking-wider">Emergency</p>
                        <p class="font-bold text-sm">+255 678 233 736</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why Choose Us --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Why Choose Us?</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Specialized Reproductive Health Care</h2>
            <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Confidential Care --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-lg mb-2">Confidential Care</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Complete privacy and confidentiality for all reproductive health services — your trust is our foundation.</p>
            </div>
            {{-- Expert Team --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-gold-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-lg mb-2">Expert Team</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Specialized healthcare professionals in reproductive health and family planning dedicated to your wellbeing.</p>
            </div>
            {{-- Comprehensive Services --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-lg mb-2">Comprehensive Services</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Full range of family planning methods and reproductive health support tailored to your unique needs.</p>
            </div>
            {{-- Compassionate Support --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-xl bg-gold-50 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="font-bold text-emerald-900 text-lg mb-2">Compassionate Support</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Empathetic care and counseling to support you and your family through every stage of your reproductive journey.</p>
            </div>
        </div>
    </div>
</section>

{{-- Our Services --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Our Services</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Reproductive Health & Family Planning</h2>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Comprehensive, confidential, and compassionate reproductive health services designed to empower you at every stage of your family planning journey.</p>
            <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                @foreach($services as $service)
                    <div class="card-hover bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <h3 class="font-bold text-emerald-900 text-lg mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $service->description ?? 'Professional reproductive health service tailored to your needs.' }}</p>
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                            Read more
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endforeach
            @else
                @foreach($defaultServices as $svc)
                    <div class="card-hover bg-gray-50 rounded-2xl p-6 border border-gray-100">
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
            @endif
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('public.services') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-lg shadow-sm hover:shadow-md transition-all">
                View All Services
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Book Appointment --}}
<section class="py-20 bg-emerald-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="{{ asset('1411.jpg') }}" alt="" class="w-full h-full object-cover">
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Left: Info --}}
            <div>
                <span class="text-gold-400 font-bold text-sm uppercase tracking-wider">Book Appointment</span>
                <h2 class="mt-2 text-3xl font-extrabold text-white">Schedule Your Visit Today</h2>
                <p class="mt-4 text-emerald-100/80 leading-relaxed">Book your appointment online and our team will confirm your visit via SMS. No registration required - just fill in your details and we'll take care of the rest.</p>
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gold-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">Easy Booking</h4>
                            <p class="text-emerald-200/70 text-sm">Fill the form and get instant confirmation</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gold-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">No Registration</h4>
                            <p class="text-emerald-200/70 text-sm">We'll create your profile automatically</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gold-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">SMS Confirmation</h4>
                            <p class="text-emerald-200/70 text-sm">Receive confirmation via SMS instantly</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Form --}}
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <h3 class="text-xl font-extrabold text-emerald-900 mb-6">Book Your Appointment</h3>
                <form method="POST" action="{{ route('public.appointments.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">FULL NAME *</label>
                            <input type="text" name="full_name" required placeholder="Enter your full name" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PHONE NUMBER *</label>
                            <input type="tel" name="phone" required placeholder="e.g. 0678233736" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">EMAIL ADDRESS</label>
                            <input type="email" name="email" placeholder="you@example.com" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">DATE OF BIRTH</label>
                            <input type="date" name="date_of_birth" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PREFERRED DATE *</label>
                            <input type="date" name="preferred_date" required min="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PREFERRED TIME *</label>
                            <select name="preferred_time" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                                <option value="">Select time</option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">SERVICE TYPE *</label>
                        <select name="service_type" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            <option value="">Select service</option>
                            <option value="Family Planning Counseling">Family Planning Counseling</option>
                            <option value="Maternal & Reproductive Health">Maternal & Reproductive Health</option>
                            <option value="Pregnancy Testing & Prenatal Care">Pregnancy Testing & Prenatal Care</option>
                            <option value="Reproductive Health Education">Reproductive Health Education</option>
                            <option value="Confidential Counseling">Confidential Counseling</option>
                            <option value="General Consultation">General Consultation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">REASON FOR VISIT</label>
                        <textarea name="reason" rows="3" placeholder="Briefly describe your reason for visit..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                        Book Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Get In Touch --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-gold-600 font-bold text-sm uppercase tracking-wider">Get In Touch</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-900">Have a Question? We're Here to Help!</h2>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Our support team is available 24/7 to assist you with anything you need regarding your health and our services.</p>
            <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Contact Info --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 text-sm uppercase tracking-wider mb-2">Phone Number</h4>
                        <p class="text-sm text-gray-600">+255 678 233 736</p>
                        <p class="text-sm text-gray-600">+255 741 064 572</p>
                        <p class="text-sm text-gray-600">+255 767 825 843</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gold-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 text-sm uppercase tracking-wider mb-2">Email Address</h4>
                        <p class="text-sm text-gray-600">info@uzaziclinic.com</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 text-sm uppercase tracking-wider mb-2">Location</h4>
                        <p class="text-sm text-gray-600">Mlimani City, Dar es Salaam</p>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                <h3 class="text-xl font-extrabold text-emerald-900 mb-6">Send a Quick Message</h3>
                <form method="POST" action="{{ route('public.contact.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">YOUR NAME</label>
                        <input type="text" name="name" required placeholder="Enter your name" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">EMAIL ADDRESS</label>
                        <input type="email" name="email" required placeholder="Enter your email" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">SUBJECT</label>
                        <input type="text" name="subject" required placeholder="What are you contacting us about?" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">YOUR MESSAGE</label>
                        <textarea name="message" required rows="4" placeholder="Write your message here..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
