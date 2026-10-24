@extends('layouts.public')

@section('title', 'Home - ' . config('app.name'))

@section('content')

{{-- Contact Form --}}
<section class="relative min-h-[600px] flex items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('7678.jpg') }}" alt="Clinic" class="w-full h-full object-cover">
        <div class="absolute inset-0 hero-gradient"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 bg-white/10 text-slate-200 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-4">
                <i class="hgi-stroke hgi-flash text-base"></i>
                Professional Medical Care
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                Your Reproductive Health,<br>Our Priority
            </h1>
            <p class="mt-6 text-lg text-slate-300 max-w-xl leading-relaxed">
                Specialized reproductive health and family planning services — confidential, compassionate, and designed to empower your informed choices.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('public.appointments') }}" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-100 text-slate-800 font-bold px-6 py-3 rounded transition-colors">
                    <i class="hgi-stroke hgi-calendar-03 text-xl"></i>
                    Book Appointment
                </a>
                <a href="{{ route('public.services') }}" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border border-white/30 text-white font-semibold px-6 py-3 rounded-lg hover:bg-white/20 transition-all">
                    Our Services
                </a>
            </div>
            <div class="mt-8 flex items-center gap-4 text-white">
                <div class="flex items-center gap-2">
                    <div class="w-12 h-12 rounded-full bg-red-500/20 border border-red-400/40 flex items-center justify-center">
                        <i class="hgi-stroke hgi-call-02 text-2xl text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Emergency</p>
                        <p class="font-bold text-sm">+255 678 233 736</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-slate-500 font-bold text-sm uppercase tracking-wider">Why Choose Us?</span>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Specialized Reproductive Health Care</h2>
            <div class="mt-4 w-20 h-1 bg-slate-800 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Contact Form --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                    <i class="hgi-stroke hgi-lock text-base text-slate-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Confidential Care</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Complete privacy and confidentiality for all reproductive health services — your trust is our foundation.</p>
            </div>
            {{-- Contact Form --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                    <i class="hgi-stroke hgi-user-multiple-02 text-base text-slate-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Expert Team</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Specialized healthcare professionals in reproductive health and family planning dedicated to your wellbeing.</p>
            </div>
            {{-- Contact Form --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                    <i class="hgi-stroke hgi-file-02 text-base text-slate-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Comprehensive Services</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Full range of family planning methods and reproductive health support tailored to your unique needs.</p>
            </div>
            {{-- Contact Form --}}
            <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-14 h-14 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                    <i class="hgi-stroke hgi-favourite text-base text-slate-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-lg mb-2">Compassionate Support</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Empathetic care and counseling to support you and your family through every stage of your reproductive journey.</p>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-slate-500 font-bold text-sm uppercase tracking-wider">Our Services</span>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Reproductive Health & Family Planning</h2>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Comprehensive, confidential, and compassionate reproductive health services designed to empower you at every stage of your family planning journey.</p>
            <div class="mt-4 w-20 h-1 bg-slate-800 mx-auto rounded-full"></div>
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
                        <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center mb-4">
                            <i class="hgi-stroke hgi-plus-sign text-2xl text-slate-600"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $service->name }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ $service->description ?? 'Professional reproductive health service tailored to your needs.' }}</p>
                        <a href="{{ route('public.appointments') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                            Read more
                            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                        </a>
                    </div>
                @endforeach
            @else
                @foreach($defaultServices as $svc)
                    <div class="card-hover bg-gray-50 rounded-2xl p-6 border border-gray-100">
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
            @endif
        </div>
        <div class="text-center mt-10">
            <a href="{{ route('public.services') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-bold px-6 py-3 rounded transition-colors">
                View All Services
                <i class="hgi-stroke hgi-arrow-right-01 text-xl"></i>
            </a>
        </div>
    </div>
</section>

{{-- Contact Form --}}
<section class="py-20 bg-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="{{ asset('1411.jpg') }}" alt="" class="w-full h-full object-cover">
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {{-- Contact Form --}}
            <div>
                <span class="text-slate-400 font-bold text-sm uppercase tracking-wider">Book Appointment</span>
                <h2 class="mt-2 text-3xl font-extrabold text-white">Schedule Your Visit Today</h2>
                <p class="mt-4 text-slate-300 leading-relaxed">Book your appointment online and our team will confirm your visit via SMS. No registration required - just fill in your details and we'll take care of the rest.</p>
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="hgi-stroke hgi-checkmark-circle-02 text-xl text-slate-300"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">Easy Booking</h4>
                            <p class="text-slate-400 text-sm">Fill the form and get instant confirmation</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="hgi-stroke hgi-user text-xl text-slate-300"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">No Registration</h4>
                            <p class="text-slate-400 text-sm">We'll create your profile automatically</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-md bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="hgi-stroke hgi-call-02 text-xl text-slate-300"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-sm">SMS Confirmation</h4>
                            <p class="text-slate-400 text-sm">Receive confirmation via SMS instantly</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8">
                <h3 class="text-xl font-extrabold text-slate-900 mb-6">Book Your Appointment</h3>
                <form method="POST" action="{{ route('public.appointments.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">FULL NAME *</label>
                            <x-ui.input type="text" name="full_name" required placeholder="Enter your full name" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PHONE NUMBER *</label>
                            <x-ui.input type="tel" name="phone" required placeholder="e.g. 0678233736" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">EMAIL ADDRESS</label>
                            <x-ui.input type="email" name="email" placeholder="you@example.com" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">DATE OF BIRTH</label>
                            <x-ui.input type="text" data-fp-date name="date_of_birth" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PREFERRED DATE *</label>
                            <x-ui.input type="text" data-fp-date name="preferred_date" required min="{{ date('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">PREFERRED TIME *</label>
                            <x-ui.select name="preferred_time" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all">
                                <option value="">Select time</option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                            </x-ui.select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">SERVICE TYPE *</label>
                        <x-ui.select name="service_type" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all">
                            <option value="">Select service</option>
                            <option value="Family Planning Counseling">Family Planning Counseling</option>
                            <option value="Maternal & Reproductive Health">Maternal & Reproductive Health</option>
                            <option value="Pregnancy Testing & Prenatal Care">Pregnancy Testing & Prenatal Care</option>
                            <option value="Reproductive Health Education">Reproductive Health Education</option>
                            <option value="Confidential Counseling">Confidential Counseling</option>
                            <option value="General Consultation">General Consultation</option>
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">REASON FOR VISIT</label>
                        <x-ui.textarea name="reason" rows="3" placeholder="Briefly describe your reason for visit..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all"></x-ui.textarea>
                    </div>
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded transition-colors">
                        Book Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-slate-500 font-bold text-sm uppercase tracking-wider">Get In Touch</span>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Have a Question? We're Here to Help!</h2>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Our support team is available 24/7 to assist you with anything you need regarding your health and our services.</p>
            <div class="mt-4 w-20 h-1 bg-slate-800 mx-auto rounded-full"></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Contact Form --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="hgi-stroke hgi-call-02 text-2xl text-slate-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-2">Phone Number</h4>
                        <p class="text-sm text-gray-600">+255 678 233 736</p>
                        <p class="text-sm text-gray-600">+255 741 064 572</p>
                        <p class="text-sm text-gray-600">+255 767 825 843</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="hgi-stroke hgi-mail-01 text-2xl text-slate-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-2">Email Address</h4>
                        <p class="text-sm text-gray-600">info@uzaziclinic.com</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="hgi-stroke hgi-location-01 text-2xl text-slate-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-2">Location</h4>
                        <p class="text-sm text-gray-600">Mlimani City, Dar es Salaam</p>
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                <h3 class="text-xl font-extrabold text-slate-900 mb-6">Send a Quick Message</h3>
                <form method="POST" action="{{ route('public.contact.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">YOUR NAME</label>
                        <x-ui.input type="text" name="name" required placeholder="Enter your name" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">EMAIL ADDRESS</label>
                        <x-ui.input type="email" name="email" required placeholder="Enter your email" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">SUBJECT</label>
                        <x-ui.input type="text" name="subject" required placeholder="What are you contacting us about?" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">YOUR MESSAGE</label>
                        <x-ui.textarea name="message" required rows="4" placeholder="Write your message here..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all"></x-ui.textarea>
                    </div>
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded transition-colors">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
