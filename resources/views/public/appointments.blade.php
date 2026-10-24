@extends('layouts.public')

@section('title', 'Book Appointment - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Book an Appointment</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Schedule your visit today. No registration required - just fill in your details.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="text-center p-4 rounded-md bg-slate-50 border border-gray-200">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-3xl mx-auto text-slate-600 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-800">Easy Booking</p>
                    <p class="text-[10px] text-gray-500">Fill the form and get instant confirmation</p>
                </div>
                <div class="text-center p-4 rounded-md bg-slate-50 border border-gray-200">
                    <i class="hgi-stroke hgi-user text-3xl mx-auto text-slate-600 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-800">No Registration</p>
                    <p class="text-[10px] text-gray-500">We'll create your profile automatically</p>
                </div>
                <div class="text-center p-4 rounded-md bg-slate-50 border border-gray-200">
                    <i class="hgi-stroke hgi-call-02 text-3xl mx-auto text-slate-600 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-800">SMS Confirmation</p>
                    <p class="text-[10px] text-gray-500">Receive confirmation via SMS instantly</p>
                </div>
            </div>

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
                    <x-ui.textarea name="reason" rows="4" placeholder="Briefly describe your reason for visit..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all"></x-ui.textarea>
                </div>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded transition-colors">
                    Book Appointment
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
