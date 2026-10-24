@extends('layouts.public')

@section('title', 'Contact Us - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Contact Us</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Have a question? We're here to help! Our support team is available 24/7.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
                        <x-ui.textarea name="message" required rows="5" placeholder="Write your message here..." class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all"></x-ui.textarea>
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
