<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <title>@yield('title', config('app.name', 'Uzazi Clinic'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .nav-link { transition: all 0.2s ease; }
        .nav-link:hover { color: #f9ac00; }
        .hero-gradient { background: linear-gradient(135deg, rgba(2,73,56,0.85) 0%, rgba(1,36,31,0.75) 50%, rgba(2,73,56,0.6) 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(2,73,56,0.25); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #024938; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f9ac00; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800">

    {{-- Top Bar --}}
    <div class="bg-emerald-900 text-emerald-100 text-xs py-2 px-4 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    +255 678 233 736
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    info@uzaziclinic.com
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span>EMERGENCY: +255 678 233 736</span>
                <a href="{{ route('login') }}" class="bg-gold-500 hover:bg-gold-600 text-white px-3 py-1 rounded-md font-semibold transition-colors">Staff Portal</a>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('public.home') }}" class="flex items-center gap-2 flex-shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-10 h-10 rounded-xl object-cover shadow-md">
                    <div class="hidden sm:block">
                        <span class="block text-emerald-800 font-extrabold text-sm leading-tight">Uzazi</span>
                        <span class="block text-gold-600 font-semibold text-[10px] leading-tight">Clinic</span>
                    </div>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex items-center gap-6">
                    <a href="{{ route('public.home') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.home') ? 'text-gold-600' : '' }}">Home</a>
                    <a href="{{ route('public.about') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.about') ? 'text-gold-600' : '' }}">About Us</a>
                    <a href="{{ route('public.branches') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.branches') ? 'text-gold-600' : '' }}">Branches</a>
                    <a href="{{ route('public.appointments') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.appointments') ? 'text-gold-600' : '' }}">Appointments</a>
                    <a href="{{ route('public.services') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.services') ? 'text-gold-600' : '' }}">Services</a>
                    <a href="{{ route('public.blog') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.blog') ? 'text-gold-600' : '' }}">Blog</a>
                    <a href="{{ route('public.shop') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.shop') ? 'text-gold-600' : '' }}">Shop</a>
                    <a href="{{ route('public.contact') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.contact') ? 'text-gold-600' : '' }}">Contact Us</a>
                </div>

                {{-- CTA --}}
                <div class="hidden lg:flex items-center gap-3">
                    <a href="{{ route('public.appointments') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-all">
                        Book Appointment
                    </a>
                </div>

                {{-- Mobile toggle --}}
                <button type="button" onclick="toggleMobileNav()" class="lg:hidden p-2 rounded-lg text-emerald-800 hover:bg-emerald-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile Nav --}}
        <div id="mobileNav" class="lg:hidden hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('public.home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.home') ? 'bg-emerald-50 text-gold-600' : '' }}">Home</a>
                <a href="{{ route('public.about') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.about') ? 'bg-emerald-50 text-gold-600' : '' }}">About Us</a>
                <a href="{{ route('public.branches') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.branches') ? 'bg-emerald-50 text-gold-600' : '' }}">Branches</a>
                <a href="{{ route('public.appointments') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.appointments') ? 'bg-emerald-50 text-gold-600' : '' }}">Appointments</a>
                <a href="{{ route('public.services') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.services') ? 'bg-emerald-50 text-gold-600' : '' }}">Services</a>
                <a href="{{ route('public.blog') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.blog') ? 'bg-emerald-50 text-gold-600' : '' }}">Blog</a>
                <a href="{{ route('public.shop') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.shop') ? 'bg-emerald-50 text-gold-600' : '' }}">Shop</a>
                <a href="{{ route('public.contact') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 {{ request()->routeIs('public.contact') ? 'bg-emerald-50 text-gold-600' : '' }}">Contact Us</a>
                <a href="{{ route('public.appointments') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-white bg-emerald-600 text-center mt-2">Book Appointment</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-emerald-700 hover:bg-emerald-50 text-center">Staff Portal</a>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('status') || session('error') || session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 5000, timerProgressBar: true });
            @if(session('status')) Toast.fire({ icon: 'success', title: '{{ session('status') }}' }); @endif
            @if(session('success')) Toast.fire({ icon: 'success', title: '{{ session('success') }}' }); @endif
            @if(session('error')) Toast.fire({ icon: 'error', title: '{{ session('error') }}' }); @endif
        });
    </script>
    @endif

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="bg-emerald-900 text-emerald-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- About --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-10 h-10 rounded-xl object-cover">
                        <div>
                            <span class="block text-white font-extrabold text-sm leading-tight">Uzazi</span>
                            <span class="block text-gold-400 font-semibold text-[10px] leading-tight">Clinic</span>
                        </div>
                    </div>
                    <p class="text-sm text-emerald-200/80 leading-relaxed">
                        Trusted Reproductive Health Clinic. Specialized reproductive health and family planning clinic delivering confidential, compassionate, and quality care. Empowering informed choices for individuals and families.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-emerald-200/80">
                        <p class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Mlimani City, Dar es Salaam, Tanzania</p>
                        <p class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> info@uzaziclinic.com</p>
                        <p class="flex items-center gap-2"><svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.289a11.025 11.025 0 005.516 5.516l1.289-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> +255 700 000 000</p>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.home') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Home</a></li>
                        <li><a href="{{ route('public.about') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">About Us</a></li>
                        <li><a href="{{ route('public.services') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Services</a></li>
                        <li><a href="{{ route('public.blog') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Blog</a></li>
                        <li><a href="{{ route('public.appointments') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Appointments</a></li>
                        <li><a href="{{ route('public.contact') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Resources --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Guidelines</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Health Tips</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">News</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Research</a></li>
                        <li><a href="{{ route('login') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Staff Portal</a></li>
                    </ul>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-3 mt-6">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">FAQs</a></li>
                        <li><a href="{{ route('login') }}" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Login</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                {{-- Legal + Newsletter --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Cookie Policy</a></li>
                        <li><a href="#" class="text-emerald-200/80 hover:text-gold-400 transition-colors">Data Protection</a></li>
                    </ul>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-3 mt-6">Stay Updated</h4>
                    <p class="text-xs text-emerald-200/70 mb-2">Subscribe for announcements, new features, and clinic updates.</p>
                    <form onsubmit="event.preventDefault(); Swal.fire({icon:'success',title:'Subscribed!',text:'You will receive updates soon.',timer:3000}); this.reset();" class="flex gap-2">
                        <input type="email" placeholder="Your email address" required class="flex-1 px-3 py-2 rounded-lg bg-emerald-800 border border-emerald-700 text-white text-sm placeholder-emerald-300/50 focus:border-gold-400 focus:ring-1 focus:ring-gold-400 outline-none">
                        <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-emerald-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-emerald-200/60">&copy; {{ date('Y') }} Uzazi Clinic. All rights reserved.</p>
                <div class="flex items-center gap-4 text-xs">
                    <a href="{{ route('public.home') }}" class="text-emerald-200/60 hover:text-gold-400 transition-colors">Home</a>
                    <span class="text-emerald-700">|</span>
                    <a href="{{ route('login') }}" class="text-emerald-200/60 hover:text-gold-400 transition-colors">Staff Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    @stack('scripts')
</body>
</html>
