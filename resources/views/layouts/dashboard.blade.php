<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ' . config('app.name', 'Laravel'))</title>

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
        .sidebar-link { transition: all 0.2s ease; position: relative; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(249,172,0,0.14); color: #fff; }
        .sidebar-link.active svg { color: #f9ac00; }
        .sidebar-submenu { max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.25s ease; transform-origin: top; }
        .sidebar-submenu.open { max-height: 600px; opacity: 1; }
        .submenu-item { position: relative; transition: all 0.2s ease; }
        .submenu-item:hover { background: rgba(255,255,255,0.05); padding-left: 1rem; }
        .submenu-item.active { color: #f9ac00 !important; font-weight: 600; background: rgba(249,172,0,0.08); }
        .arrow-icon { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .arrow-icon.rotate-180 { transform: rotate(180deg); }
        .group-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.35); padding: 0.5rem 0.75rem; margin-top: 0.5rem; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #01241f; }
        ::-webkit-scrollbar-thumb { background: #024938; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f9ac00; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 text-slate-800">

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="dashboardSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-emerald-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-emerald-800/50 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-extrabold text-sm">
                {{ strtoupper(substr(config('app.name', 'L'), 0, 1)) }}
            </div>
            <span class="ml-2 text-white font-bold text-sm tracking-wide uppercase">{{ config('app.name', 'Laravel') }}</span>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Dashboard --}}
            <div class="sidebar-group">
                @if(auth()->user()->isDoctor())
                <a href="{{ route('dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>My Dashboard</span>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
                @endif
            </div>

            {{-- Reception --}}
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-reception')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('reception.*','patients.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Reception</span>
                    </div>
                    <svg id="arrow-reception" class="w-3 h-3 arrow-icon {{ request()->routeIs('reception.*','patients.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-reception" class="sidebar-submenu {{ request()->routeIs('reception.*','patients.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('reception.dashboard') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg> Reception Dashboard</span>
                    </a>
                    <a href="{{ route('patients.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Patients</span>
                    </a>
                </div>
            </div>

            {{-- Doctor --}}
            @if(auth()->user()->isDoctor())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2">
                    <span class="text-[10px] uppercase tracking-wider text-gold-400 font-bold">Doctor Workspace</span>
                </div>
                <a href="{{ route('doctor.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('doctor.queue') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </div>
                    <span>My Queue</span>
                </a>
                <a href="{{ route('doctor.lab-results') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('doctor.lab-results') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
                        </span>
                    </div>
                    <span>Lab Results</span>
                </a>
                <a href="{{ route('appointments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('appointments.*') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Appointments</span>
                </a>
                <a href="{{ route('patients.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('patients.*') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>My Patients</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all mb-1">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span id="doctorChatUnreadBadge" class="hidden absolute -top-1 -right-1 bg-gold-500 text-white text-[8px] font-bold w-3.5 h-3.5 flex items-center justify-center rounded-full">0</span>
                    </div>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('profile') ? 'bg-emerald-700/60 text-white shadow-md' : 'hover:bg-emerald-700/40' }} transition-all">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>My Profile</span>
                </a>
            </div>
            @else
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-doctor')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        <span>Doctor</span>
                    </div>
                    <svg id="arrow-doctor" class="w-3 h-3 arrow-icon {{ request()->routeIs('doctor.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-doctor" class="sidebar-submenu {{ request()->routeIs('doctor.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('doctor.queue') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('doctor.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg> My Queue</span>
                    </a>
                    <a href="{{ route('appointments.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Appointments</span>
                    </a>
                    <a href="{{ route('patients.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Patients</span>
                    </a>
                </div>
            </div>
            @endif

            @if(!auth()->user()->isDoctor())
            {{-- Clinical Records --}}
            <div class="sidebar-group">
                <a href="{{ route('clinical-records.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('clinical-records.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Clinical Records</span>
                </a>
            </div>

            {{-- Lab Management --}}
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-lab')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Lab Management</span>
                    </div>
                    <svg id="arrow-lab" class="w-3 h-3 arrow-icon {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-lab" class="sidebar-submenu {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('lab.queue') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg> Lab Queue</span>
                    </a>
                    <a href="{{ route('lab-tests.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab-tests.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg> Test Types</span>
                    </a>
                    <a href="{{ route('lab-equipment.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab-equipment.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg> Lab Equipment</span>
                    </a>
                </div>
            </div>

            {{-- Pharmacy & Inventory --}}
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-pharmacy')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 00-4-4H5.52a2 2 0 00-1.98 1.73l-.43 3.02a2 2 0 001.66 2.25L8 10V5m4 3v13m0-13V6a4 4 0 014-4h2.48a2 2 0 011.98 1.73l.43 3.02a2 2 0 01-1.66 2.25L16 10V5m-4 8h.01M8 16h.01M12 16h.01M16 16h.01"/></svg>
                        <span>Pharmacy & Inventory</span>
                    </div>
                    <svg id="arrow-pharmacy" class="w-3 h-3 arrow-icon {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-pharmacy" class="sidebar-submenu {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('pharmacy.queue') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('pharmacy.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg> Pharmacy Queue</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Inventory</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg> Categories</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Suppliers</span>
                    </a>
                </div>
            </div>

            {{-- Appointments --}}
            <div class="sidebar-group">
                <a href="{{ route('appointments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Appointments</span>
                </a>
            </div>

            {{-- Clinic Management --}}
            <div class="sidebar-group">
                <a href="{{ route('clinic-rooms.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('clinic-rooms.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Clinic Rooms</span>
                </a>
            </div>

            {{-- Medical Staff / HR --}}
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-hr')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Medical Staff / HR</span>
                    </div>
                    <svg id="arrow-hr" class="w-3 h-3 arrow-icon {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-hr" class="sidebar-submenu {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*','admin.queue') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('admin.queue') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('admin.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Patient Queue</span>
                    </a>
                    <a href="{{ route('admin.doctors.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg> Doctors Management</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> User Accounts</span>
                    </a>
                    <a href="{{ route('departments.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg> Departments</span>
                    </a>
                    <a href="{{ route('shifts.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Shifts</span>
                    </a>
                </div>
            </div>

            {{-- Blog --}}
            <div class="sidebar-group">
                <a href="{{ route('posts.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('posts.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span>Blog</span>
                </a>
            </div>

            {{-- Financial Management --}}
            <div class="sidebar-group">
                <a href="{{ route('invoices.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14m-5.25-3.334L9 14m3.25-3.334L14 14M12 3.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zM6 20.25h12a2.25 2.25 0 002.25-2.25V5.75A2.25 2.25 0 0018 3.5H6A2.25 2.25 0 003.75 5.75v12.25A2.25 2.25 0 006 20.25z"/></svg>
                    <span>Invoices</span>
                </a>
            </div>

            {{-- Reports --}}
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-reports')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Reports</span>
                    </div>
                    <svg id="arrow-reports" class="w-3 h-3 arrow-icon {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-reports" class="sidebar-submenu {{ request()->routeIs('reports.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('reports.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg> Overview</span>
                    </a>
                    <a href="{{ route('reports.sales') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Sales Report</span>
                    </a>
                    <a href="{{ route('reports.patients') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.patients') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Patient Report</span>
                    </a>
                    <a href="{{ route('reports.doctors') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.doctors') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg> Doctor Performance</span>
                    </a>
                    <a href="{{ route('reports.stock') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Stock Report</span>
                    </a>
                    <a href="{{ route('reports.revenue') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.revenue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Revenue Report</span>
                    </a>
                    <a href="{{ route('reports.health') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.health') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg> System Health</span>
                    </a>
                </div>
            </div>

            {{-- Communications --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-communications')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('notifications.*','sms.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Communications</span>
                    </div>
                    <svg id="arrow-communications" class="w-3 h-3 arrow-icon {{ request()->routeIs('notifications.*','sms.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-communications" class="sidebar-submenu {{ request()->routeIs('notifications.*','sms.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('notifications.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Send Notification</span>
                    </a>
                    <a href="{{ route('notifications.templates') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('notifications.templates') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Templates</span>
                    </a>
                    <a href="{{ route('sms.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.index','sms.store') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg> Send SMS</span>
                    </a>
                    <a href="{{ route('sms.logs') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.logs') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> SMS Logs</span>
                    </a>
                    <a href="{{ route('sms.templates') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.templates') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg> SMS Templates</span>
                    </a>
                </div>
            </div>

            {{-- Chat --}}
            <div class="sidebar-group">
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1 1 0 01-1-1v-6a1 1 0 011-1h9a1 1 0 011 1v6a1 1 0 01-1 1h-2v4l-4-4H7a2 2 0 01-2-2V6a2 2 0 012-2h7a2 2 0 012 2v2z"/></svg>
                    <span>Chat</span>
                    <span id="chatUnreadBadge" class="hidden ml-auto bg-gold-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
                </a>
            </div>

            {{-- Audit Logs --}}
            <div class="sidebar-group">
                <a href="{{ route('activity_logs.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Audit Logs</span>
                </a>
            </div>

            {{-- Settings --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-settings')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-emerald-100 text-sm font-medium {{ request()->routeIs('settings.*','services.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Settings</span>
                    </div>
                    <svg id="arrow-settings" class="w-3 h-3 arrow-icon {{ request()->routeIs('settings.*','services.*') ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-settings" class="sidebar-submenu {{ request()->routeIs('settings.*','services.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('settings.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg> General</span>
                    </a>
                    <a href="{{ route('settings.email') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.email') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Email Config</span>
                    </a>
                    <a href="{{ route('settings.sms') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.sms') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg> SMS Gateway</span>
                    </a>
                    <a href="{{ route('settings.payment') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.payment') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Payment Gateways</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="submenu-item block text-xs text-emerald-100/80 hover:text-white py-1.5 pl-3 {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg> Services & Prices</span>
                    </a>
                </div>
            </div>
            @endif

        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-emerald-800/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-emerald-300/60">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('dashboard-logout').submit();" class="text-emerald-300/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
                <form id="dashboard-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4">
                {{-- Notifications --}}
                <div class="relative">
                    <button onclick="document.getElementById('notificationDropdown').classList.toggle('hidden')" class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </button>
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-xl border border-gray-100 shadow-lg z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-900">Notifications</p>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-6 text-center text-xs text-gray-400">No new notifications</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6 animate-fade relative">
            @yield('content')
        </main>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('dashboardSidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id.replace('menu-', ''));
            menu.classList.toggle('open');
            if (arrow) arrow.classList.toggle('rotate-180');
        }

        // SweetAlert2 side toasts
        (function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('status'))
                Toast.fire({ icon: 'success', title: '{{ session('status') }}' });
            @endif
            @if(session('error'))
                Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
            @endif
            @if(session('warning'))
                Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
            @endif
            @if(session('info'))
                Toast.fire({ icon: 'info', title: '{{ session('info') }}' });
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    Toast.fire({ icon: 'error', title: '{{ $error }}' });
                @endforeach
            @endif
        })();

        // Generic AJAX form helper (used by dashboard slide-overs)
        function setupAjaxForm(selector, successMessage, onSuccess) {
            const form = document.querySelector(selector);
            if (!form) return;
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = this.querySelector('button[type="submit"]');
                const original = btn ? btn.textContent : 'Submit';
                if (btn) { btn.disabled = true; btn.textContent = 'Inahifadhi...'; }
                try {
                    const response = await fetch(this.action, {
                        method: this.method.toUpperCase(),
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value },
                        credentials: 'same-origin'
                    });
                    const data = await response.json().catch(() => null);
                    if (!response.ok) throw new Error(data?.message || 'Something went wrong');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data?.message || successMessage, showConfirmButton: false, timer: 3000 });
                    if (onSuccess) onSuccess();
                    form.reset();
                } catch (err) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: err.message, showConfirmButton: false, timer: 4000 });
                } finally {
                    if (btn) { btn.disabled = false; btn.textContent = original; }
                }
            });
        }

        // AJAX form handler with SweetAlert confirmation
        document.querySelectorAll('form[data-ajax]').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const confirmMessage = this.dataset.confirm;
                if (confirmMessage) {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: confirmMessage,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#024938',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, proceed!'
                    });
                    if (!result.isConfirmed) return;
                }
                const formData = new FormData(this);
                const method = this.method.toUpperCase();
                const action = this.action;
                const btn = this.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span> Processing'; }

                try {
                    const response = await fetch(action, {
                        method: method,
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                    if (response.redirected) { window.location.href = response.url; return; }
                    const data = await response.json().catch(() => null);
                    Swal.fire({ icon: 'success', title: data?.message || 'Done', timer: 2000, showConfirmButton: false });
                    if (!data?.redirect) setTimeout(() => window.location.reload(), 1000);
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Something went wrong' });
                } finally {
                    if (btn) { btn.disabled = false; btn.innerHTML = btn.dataset.original || 'Submit'; }
                }
            });
        });

        // Chat unread badge polling
        (function() {
            const badge = document.getElementById('chatUnreadBadge');
            if (!badge) return;
            async function updateBadge() {
                try {
                    const res = await fetch('{{ route("chat.unread-count") }}', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                    const data = await res.json();
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                } catch (e) {}
            }
            updateBadge();
            setInterval(updateBadge, 10000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
