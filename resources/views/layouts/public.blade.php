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
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar { font-family: inherit; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.08); }
        .flatpickr-day.selected, .flatpickr-day.selected:hover, .flatpickr-day.selected:focus { background: #1e293b; border-color: #1e293b; }
        .flatpickr-day.today { border-color: #94a3b8; }
        .flatpickr-day:hover { background: #f1f5f9; border-color: #f1f5f9; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    @vite(['resources/css/app.css'])
    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .nav-link { transition: all 0.2s ease; }
        .nav-link:hover { color: #94a3b8; }
        .hero-gradient { background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.7) 50%, rgba(30,41,59,0.6) 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(15,23,42,0.25); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800">
    <x-ui.page-loader />

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1">
                    <i class="hgi-stroke hgi-call-02 text-sm"></i>
                    +255 678 233 736
                </span>
                <span class="flex items-center gap-1">
                    <i class="hgi-stroke hgi-mail-01 text-sm"></i>
                    info@uzaziclinic.com
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span>EMERGENCY: +255 678 233 736</span>
                <a href="{{ route('login') }}" class="bg-white hover:bg-slate-100 text-slate-800 px-3 py-1 rounded font-semibold transition-colors">Staff Portal</a>
            </div>
        </div>
    </div>

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <a href="{{ route('public.home') }}" class="flex items-center gap-2 flex-shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-10 h-10 rounded-xl object-cover shadow-md">
                    <div class="hidden sm:block">
                        <span class="block text-slate-800 font-extrabold text-sm leading-tight">Uzazi</span>
                        <span class="block text-slate-500 font-semibold text-[10px] leading-tight">Clinic</span>
                    </div>
                </a>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div class="hidden lg:flex items-center gap-6">
                    <a href="{{ route('public.home') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.home') ? 'text-slate-900 underline underline-offset-4' : '' }}">Home</a>
                    <a href="{{ route('public.about') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.about') ? 'text-slate-900 underline underline-offset-4' : '' }}">About Us</a>
                    <a href="{{ route('public.branches') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.branches') ? 'text-slate-900 underline underline-offset-4' : '' }}">Branches</a>
                    <a href="{{ route('public.appointments') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.appointments') ? 'text-slate-900 underline underline-offset-4' : '' }}">Appointments</a>
                    <a href="{{ route('public.services') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.services') ? 'text-slate-900 underline underline-offset-4' : '' }}">Services</a>
                    <a href="{{ route('public.blog') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.blog') ? 'text-slate-900 underline underline-offset-4' : '' }}">Blog</a>
                    <a href="{{ route('public.shop') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.shop') ? 'text-slate-900 underline underline-offset-4' : '' }}">Shop</a>
                    <a href="{{ route('public.contact') }}" class="nav-link text-sm font-semibold text-slate-700 {{ request()->routeIs('public.contact') ? 'text-slate-900 underline underline-offset-4' : '' }}">Contact Us</a>
                </div>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div class="hidden lg:flex items-center gap-3">
                    <a href="{{ route('public.appointments') }}" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold px-5 py-2.5 rounded transition-colors">
                        Book Appointment
                    </a>
                </div>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <button type="button" onclick="toggleMobileNav()" class="lg:hidden p-2 rounded-lg text-slate-800 hover:bg-gray-100">
                    <i class="hgi-stroke hgi-menu-01 text-2xl"></i>
                </button>
            </div>
        </div>

        <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
        <div id="mobileNav" class="lg:hidden hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('public.home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.home') ? 'bg-gray-100 text-slate-900' : '' }}">Home</a>
                <a href="{{ route('public.about') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.about') ? 'bg-gray-100 text-slate-900' : '' }}">About Us</a>
                <a href="{{ route('public.branches') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.branches') ? 'bg-gray-100 text-slate-900' : '' }}">Branches</a>
                <a href="{{ route('public.appointments') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.appointments') ? 'bg-gray-100 text-slate-900' : '' }}">Appointments</a>
                <a href="{{ route('public.services') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.services') ? 'bg-gray-100 text-slate-900' : '' }}">Services</a>
                <a href="{{ route('public.blog') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.blog') ? 'bg-gray-100 text-slate-900' : '' }}">Blog</a>
                <a href="{{ route('public.shop') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.shop') ? 'bg-gray-100 text-slate-900' : '' }}">Shop</a>
                <a href="{{ route('public.contact') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 {{ request()->routeIs('public.contact') ? 'bg-gray-100 text-slate-900' : '' }}">Contact Us</a>
                <a href="{{ route('public.appointments') }}" class="block px-3 py-2 rounded-lg text-sm font-bold text-white bg-slate-800 text-center mt-2">Book Appointment</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-gray-100 text-center">Staff Portal</a>
            </div>
        </div>
    </nav>

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    @if(session('status') || session('error') || session('success'))
    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    @endif

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    @yield('content')

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    <footer class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-10 h-10 rounded-xl object-cover">
                        <div>
                            <span class="block text-white font-extrabold text-sm leading-tight">Uzazi</span>
                            <span class="block text-slate-400 font-semibold text-[10px] leading-tight">Clinic</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Trusted Reproductive Health Clinic. Specialized reproductive health and family planning clinic delivering confidential, compassionate, and quality care. Empowering informed choices for individuals and families.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-slate-400">
                        <p class="flex items-center gap-2"><i class="hgi-stroke hgi-location-01 text-base text-slate-500"></i> Mlimani City, Dar es Salaam, Tanzania</p>
                        <p class="flex items-center gap-2"><i class="hgi-stroke hgi-mail-01 text-base text-slate-500"></i> info@uzaziclinic.com</p>
                        <p class="flex items-center gap-2"><i class="hgi-stroke hgi-call-02 text-base text-slate-500"></i> +255 700 000 000</p>
                    </div>
                </div>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('public.home') }}" class="text-slate-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('public.about') }}" class="text-slate-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('public.services') }}" class="text-slate-400 hover:text-white transition-colors">Services</a></li>
                        <li><a href="{{ route('public.blog') }}" class="text-slate-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="{{ route('public.appointments') }}" class="text-slate-400 hover:text-white transition-colors">Appointments</a></li>
                        <li><a href="{{ route('public.contact') }}" class="text-slate-400 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Guidelines</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Health Tips</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">News</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Research</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Staff Portal</a></li>
                    </ul>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-3 mt-6">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">FAQs</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Login</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Cookie Policy</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Data Protection</a></li>
                    </ul>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-3 mt-6">Stay Updated</h4>
                    <p class="text-xs text-slate-400 mb-2">Subscribe for announcements, new features, and clinic updates.</p>
                    <form onsubmit="event.preventDefault(); Swal.fire({icon:'success',title:'Subscribed!',text:'You will receive updates soon.',timer:3000}); this.reset();" class="flex gap-2">
                        <x-ui.input type="email" placeholder="Your email address" required class="flex-1 px-3 py-2 rounded bg-slate-800 border border-slate-700 text-white text-sm placeholder-slate-500 focus:border-slate-500 focus:ring-1 focus:ring-slate-500 outline-none" />
                        <button type="submit" class="bg-white hover:bg-slate-100 text-slate-800 px-3 py-2 rounded text-sm font-semibold transition-colors">
                            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                        </button>
                    </form>
                </div>
            </div>

            <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
            <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Uzazi Clinic. All rights reserved.</p>
                <div class="flex items-center gap-4 text-xs">
                    <a href="{{ route('public.home') }}" class="text-slate-500 hover:text-white transition-colors">Home</a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ route('login') }}" class="text-slate-500 hover:text-white transition-colors">Staff Portal</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('focusin', function(e) {
        var el = e.target;
        if (!(el instanceof HTMLElement) || el._flatpickr || el.classList.contains('flatpickr-input')) return;
        var cfg = null;
        if (el.matches('input[data-fp-date]')) cfg = { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', allowInput: true };
        else if (el.matches('input[data-fp-datetime]')) cfg = { enableTime: true, time_24hr: true, dateFormat: 'Y-m-d\\TH:i', altInput: true, altFormat: 'd M Y h:i K', allowInput: true };
        else if (el.matches('input[data-fp-time]')) cfg = { enableTime: true, noCalendar: true, dateFormat: 'H:i', altInput: true, altFormat: 'h:i K', allowInput: true };
        if (cfg) flatpickr(el, cfg);
    }, true);
    </script>

    <script>
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    </script>
    @stack('scripts')
</body>
</html>
