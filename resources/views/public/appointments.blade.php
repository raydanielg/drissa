@extends('layouts.public')

@section('title', 'Book Appointment - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Book an Appointment</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Schedule your visit today. No registration required - just fill in your details.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="text-center p-4 rounded-xl bg-emerald-50">
                    <svg class="w-8 h-8 mx-auto text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-semibold text-emerald-800">Easy Booking</p>
                    <p class="text-[10px] text-gray-500">Fill the form and get instant confirmation</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-gold-50">
                    <svg class="w-8 h-8 mx-auto text-gold-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <p class="text-xs font-semibold text-emerald-800">No Registration</p>
                    <p class="text-[10px] text-gray-500">We'll create your profile automatically</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-emerald-50">
                    <svg class="w-8 h-8 mx-auto text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <p class="text-xs font-semibold text-emerald-800">SMS Confirmation</p>
                    <p class="text-[10px] text-gray-500">Receive confirmation via SMS instantly</p>
                </div>
            </div>

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
                    <textarea name="reason" rows="4" placeholder="Briefly describe your reason for visit..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                    Book Appointment
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
