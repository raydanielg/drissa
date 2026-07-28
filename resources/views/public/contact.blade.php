@extends('layouts.public')

@section('title', 'Contact Us - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Contact Us</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Have a question? We're here to help! Our support team is available 24/7.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
                        <textarea name="message" required rows="5" placeholder="Write your message here..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
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
