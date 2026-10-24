<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ' . config('app.name', 'Laravel'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-calendar { font-family: inherit; font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.08); }
        .flatpickr-day.selected, .flatpickr-day.selected:hover, .flatpickr-day.selected:focus { background: #1e293b; border-color: #1e293b; }
        .flatpickr-day.today { border-color: #94a3b8; }
        .flatpickr-day:hover { background: #f1f5f9; border-color: #f1f5f9; }
        .flatpickr-months .flatpickr-month { color: #1e293b; }
        .flatpickr-current-month { font-size: 95%; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Shared compact toast (reusable across all pages)
        window.AppToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 4000,
            timerProgressBar: true,
            customClass: { popup: 'app-toast' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        window.toast = (message, icon = 'success') => window.AppToast.fire({ icon, title: message });

        // Session flashes
        (function() {
            @if(session('status'))
                window.AppToast.fire({ icon: 'success', title: @json(session('status')) });
            @endif
            @if(session('success'))
                window.AppToast.fire({ icon: 'success', title: @json(session('success')) });
            @endif
            @if(session('error'))
                window.AppToast.fire({ icon: 'error', title: @json(session('error')) });
            @endif
            @if(session('warning'))
                window.AppToast.fire({ icon: 'warning', title: @json(session('warning')) });
            @endif
            @if(session('info'))
                window.AppToast.fire({ icon: 'info', title: @json(session('info')) });
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    window.AppToast.fire({ icon: 'error', title: @json($error) });
                @endforeach
            @endif
        })();

        // Close any open overlay (modal/drawer) on Escape via its backdrop
        document.addEventListener('keydown', function(e) {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden)').forEach(function(el) {
                const backdrop = el.querySelector(':scope > .absolute.inset-0');
                if (backdrop) { backdrop.click(); } else { el.classList.add('hidden'); document.body.style.overflow = ''; }
            });
        });

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
                    window.AppToast.fire({ icon: 'success', title: data?.message || successMessage });
                    if (onSuccess) onSuccess();
                    form.reset();
                } catch (err) {
                    window.AppToast.fire({ icon: 'error', title: err.message });
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
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, proceed'
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

        // Global call notification polling (reception & admin on any page)
        @if(auth()->user()->isReception() || auth()->user()->isAdmin())
        (function() {
            let lastCallCheck = new Date().toISOString();
            let shownNotificationIds = new Set();
            const alarmSound = new Audio('{{ asset("mixkit-alarm-tone-996.wav") }}');
            alarmSound.volume = 0.7;

            // Create floating notification element
            const notifContainer = document.createElement('div');
            notifContainer.id = 'globalCallNotif';
            notifContainer.className = 'fixed top-4 right-4 z-[9999] hidden';
            notifContainer.innerHTML = `
                <div class="bg-amber-50 border-2 border-amber-400 rounded-xl shadow-2xl p-4 max-w-sm animate-fade">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="hgi-stroke hgi-notification-01 text-xl text-amber-600 animate-bounce"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-amber-900" id="globalCallNotifTitle"></p>
                            <p class="text-xs text-amber-700 mt-0.5" id="globalCallNotifText"></p>
                        </div>
                        <button onclick="document.getElementById('globalCallNotif').classList.add('hidden')" class="text-amber-400 hover:text-amber-600 flex-shrink-0">
                            <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(notifContainer);

            async function pollCallNotifications() {
                try {
                    const res = await fetch('{{ route("reception.call-notifications") }}?since=' + encodeURIComponent(lastCallCheck), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    lastCallCheck = data.server_time;

                    const newNotifs = (data.notifications || []).filter(n => !shownNotificationIds.has(n.id));
                    if (newNotifs.length > 0) {
                        newNotifs.forEach(n => shownNotificationIds.add(n.id));
                        const latest = newNotifs[0];
                        document.getElementById('globalCallNotifTitle').textContent = 'Dr. ' + latest.doctor_name + ' is calling ' + latest.patient_name;
                        document.getElementById('globalCallNotifText').textContent = 'Visit #' + latest.visit_number + ' - Please direct the patient. Called at ' + latest.time;
                        notifContainer.classList.remove('hidden');

                        alarmSound.currentTime = 0;
                        alarmSound.play().catch(e => console.log('Audio blocked:', e));

                        setTimeout(() => notifContainer.classList.add('hidden'), 30000);
                    }
                } catch (e) { console.error('Call poll failed', e); }
            }

            setTimeout(pollCallNotifications, 3000);
            setInterval(pollCallNotifications, 10000);
        })();
        @endif
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }

        /* Compact app toasts — applies to every Swal toast */
        .swal2-popup.swal2-toast { padding: 10px 14px !important; border-radius: 8px !important; border: 1px solid #e5e7eb !important; box-shadow: 0 4px 16px rgba(15,23,42,.12) !important; max-width: 340px; animation-duration: .2s; }
        .swal2-popup.swal2-toast .swal2-title { font-size: 12.5px !important; font-weight: 500 !important; color: #1f2937 !important; padding: 0 !important; margin: 0 !important; text-align: left; line-height: 1.45; }
        .swal2-popup.swal2-toast .swal2-icon { width: 18px !important; height: 18px !important; min-width: 18px; margin: 0 10px 0 0 !important; border-width: 2px !important; }
        .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content { font-size: 11px !important; font-weight: 700; }
        .swal2-popup.swal2-toast .swal2-close { font-size: 14px !important; width: 18px !important; height: 18px !important; color: #9ca3af !important; padding: 0 !important; margin: -2px -4px 0 8px !important; }
        .swal2-popup.swal2-toast .swal2-close:hover { color: #4b5563 !important; }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar { height: 2px !important; background: #cbd5e1 !important; }
        .sidebar-link { transition: all 0.2s ease; position: relative; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(51,65,85,0.9); color: #fff; }
        .sidebar-link.active svg { color: #fff; }
        .sidebar-submenu { max-height: 0; opacity: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.25s ease; transform-origin: top; }
        .sidebar-submenu.open { max-height: 600px; opacity: 1; }
        .submenu-item { position: relative; transition: all 0.2s ease; }
        .submenu-item:hover { background: rgba(255,255,255,0.05); padding-left: 1rem; }
        .submenu-item.active { color: #fff !important; font-weight: 600; background: rgba(51,65,85,0.5); }
        .arrow-icon { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .arrow-icon.rotate-180 { transform: rotate(180deg); }
        .group-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.35); padding: 0.5rem 0.75rem; margin-top: 0.5rem; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
    @stack('styles')
</head>
<body class="font-['Instrument_Sans',sans-serif] antialiased bg-gray-50 text-slate-800">

    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <aside id="dashboardSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-slate-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        <div class="h-16 flex items-center px-6 border-b border-slate-700/50 flex-shrink-0">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-8 h-8 rounded-lg object-cover">
            <span class="ml-2 text-white font-bold text-sm tracking-wide uppercase">{{ config('app.name', 'Laravel') }}</span>
        </div>

        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <div class="sidebar-group">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-layout-2-column text-xl text-slate-400"></i>
                    <span>Admin Dashboard</span>
                </a>
                @endif
            </div>

            @if(auth()->user()->isReception())
            <div class="sidebar-group">
                <a href="{{ route('reception.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('reception.dashboard') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-layout-2-column text-xl text-slate-400"></i>
                    <span>Reception Dashboard</span>
                </a>
                <a href="{{ route('reception.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('reception.queue') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-file-01 text-xl text-slate-400"></i>
                    <span>Reception Queue</span>
                </a>
                <a href="{{ route('reception.payments') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('reception.payments') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-message-multiple-02 text-xl text-slate-400"></i>
                    <span>Payments</span>
                </a>
                <a href="{{ route('patients.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('patients.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                    <span>Patients</span>
                </a>
                <a href="{{ route('appointments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('appointments.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-calendar-03 text-xl text-slate-400"></i>
                    <span>Appointments</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-reception')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('reception.*','patients.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                        <span>Reception</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('reception.*','patients.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-reception" class="sidebar-submenu {{ request()->routeIs('reception.*','patients.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('reception.dashboard') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reception.dashboard') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-file-01 text-xs"></i> Reception Dashboard</span>
                    </a>
                    <a href="{{ route('patients.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-user-multiple-02 text-xs"></i> Patients</span>
                    </a>
                    <a href="{{ route('appointments.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-calendar-03 text-xs"></i> Appointments</span>
                    </a>
                    <a href="{{ route('reception.payments') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reception.payments') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-message-multiple-02 text-xs"></i> Payments</span>
                    </a>
                </div>
            </div>
            @endif

            @if(auth()->user()->isDoctor())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2">
                    <span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Doctor Workspace</span>
                </div>
                <a href="{{ route('doctor.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.dashboard') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-home-01 text-xl text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('doctor.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.queue') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <div class="relative">
                        <i class="hgi-stroke hgi-building-03 text-xl text-slate-400"></i>
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </div>
                    <span>My Queue</span>
                </a>
                <a href="{{ route('doctor.lab-results') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.lab-results') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <div class="relative">
                        <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-400"></i>
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
                        </span>
                    </div>
                    <span>Results</span>
                </a>
                <a href="{{ route('doctor.patients') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.patients') or request()->routeIs('doctor.patients.history') or request()->routeIs('patients.history') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-clipboard text-xl text-slate-400"></i>
                    <span>Patient History</span>
                </a>
                <a href="{{ route('appointments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('appointments.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-calendar-03 text-xl text-slate-400"></i>
                    <span>Appointments</span>
                </a>
                <a href="{{ route('patients.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('patients.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                    <span>My Patients</span>
                </a>
                <a href="{{ route('doctor.schedule') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.schedule') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-clock-01 text-xl text-slate-400"></i>
                    <span>Schedule</span>
                </a>
                <a href="{{ route('doctor.reports') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('doctor.reports') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-analytics-01 text-xl text-slate-400"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all mb-1">
                    <div class="relative">
                        <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                        <span id="doctorChatUnreadBadge" class="hidden absolute -top-1 -right-1 bg-amber-500 text-white text-[8px] font-bold w-3.5 h-3.5 flex items-center justify-center rounded-full">0</span>
                    </div>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'bg-slate-700 text-white' : 'hover:bg-slate-800' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isLab())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Laboratory Workspace</span></div>
                <a href="{{ route('lab.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('lab.queue') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-archive text-xl text-slate-400"></i>
                    <span>Lab Queue</span>
                </a>
                <a href="{{ route('lab.history') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('lab.history', 'lab.patient-history') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-clock-01 text-xl text-slate-400"></i>
                    <span>Lab History</span>
                </a>
                <a href="{{ route('lab-tests.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('lab-tests.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-file-01 text-xl text-slate-400"></i>
                    <span>Lab Tests</span>
                </a>
                <a href="{{ route('lab-equipment.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('lab-equipment.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-400"></i>
                    <span>Equipment</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'active' : '' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isLab() || auth()->user()->isUltrasound() || auth()->user()->isAdmin())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Ultrasound Workspace</span></div>
                <a href="{{ route('ultrasound.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('ultrasound.dashboard') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-home-01 text-xl text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('ultrasound.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('ultrasound.queue') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-analytics-02 text-xl text-slate-400"></i>
                    <span>Ultrasound Queue</span>
                </a>
                <a href="{{ route('ultrasound-services.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('ultrasound-services.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-archive text-xl text-slate-400"></i>
                    <span>Ultrasound Services</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isPharmacy())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Pharmacy Workspace</span></div>
                <a href="{{ route('pharmacy.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.dashboard') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-home-01 text-xl text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pharmacy.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.queue') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-400"></i>
                    <span>Prescription Queue</span>
                </a>
                <a href="{{ route('pharmacy.inventory') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.inventory') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-package text-xl text-slate-400"></i>
                    <span>Inventory</span>
                </a>
                <a href="{{ route('pharmacy.history') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.history') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-clock-01 text-xl text-slate-400"></i>
                    <span>History</span>
                </a>
                <a href="{{ route('pharmacy.reports') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.reports') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-analytics-01 text-xl text-slate-400"></i>
                    <span>Reports</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'active' : '' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isNurse())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Nurse Workspace</span></div>
                <a href="{{ route('nurse.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('nurse.dashboard') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-home-01 text-xl text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('nurse.queue') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('nurse.queue') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-archive text-xl text-slate-400"></i>
                    <span>Vitals Queue</span>
                </a>
                <a href="{{ route('nurse.patients') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('nurse.patients') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                    <span>Patients</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'active' : '' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isManager())
            <div class="sidebar-group">
                <div class="px-3 py-2 mb-2"><span class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Clinic Manager</span></div>
                <a href="{{ route('manager.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('manager.dashboard') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-home-01 text-xl text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('manager.reports.create') && request('type') !== 'weekly' ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-clock-01 text-xl text-slate-400"></i>
                    <span>Daily Report</span>
                </a>
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('manager.reports.create') && request('type') === 'weekly' ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-calendar-03 text-xl text-slate-400"></i>
                    <span>Weekly Report</span>
                </a>
                <a href="{{ route('manager.reports.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('manager.reports.index','manager.reports.show','manager.reports.edit') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-file-02 text-xl text-slate-400"></i>
                    <span>My Reports</span>
                </a>
                <a href="{{ route('patients.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('patients.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                    <span>Patients</span>
                </a>
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }} transition-all mb-1">
                    <i class="hgi-stroke hgi-bubble-chat text-xl text-slate-400"></i>
                    <span>Chats</span>
                </a>
                <a href="{{ route('profile') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 text-sm font-medium {{ request()->routeIs('profile') ? 'active' : '' }} transition-all">
                    <i class="hgi-stroke hgi-user text-xl text-slate-400"></i>
                    <span>My Profile</span>
                </a>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="sidebar-group">
                <a href="{{ route('analytics.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-analytics-02 text-xl text-slate-400"></i>
                    <span>Analytics</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('clinical-records.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('clinical-records.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-file-02 text-xl text-slate-400"></i>
                    <span>Clinical Records</span>
                </a>
            </div>

            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-lab')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-400"></i>
                        <span>Lab Management</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-lab" class="sidebar-submenu {{ request()->routeIs('lab.*','lab-equipment.*','lab-tests.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('lab.queue') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-archive text-xs"></i> Lab Queue</span>
                    </a>
                    <a href="{{ route('lab-tests.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab-tests.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-file-01 text-xs"></i> Test Types</span>
                    </a>
                    <a href="{{ route('lab-equipment.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('lab-equipment.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-test-tube-01 text-xs"></i> Lab Equipment</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-pharmacy')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-building-03 text-xl text-slate-400"></i>
                        <span>Pharmacy & Inventory</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-pharmacy" class="sidebar-submenu {{ request()->routeIs('pharmacy.*','products.*','categories.*','suppliers.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('pharmacy.queue') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('pharmacy.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-test-tube-01 text-xs"></i> Pharmacy Queue</span>
                    </a>
                    <a href="{{ route('pharmacy.inventory') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('pharmacy.inventory') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-test-tube-01 text-xs"></i> Medications</span>
                    </a>
                    <a href="{{ route('pharmacy.history') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('pharmacy.history') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-clock-01 text-xs"></i> Rx History</span>
                    </a>
                    <a href="{{ route('pharmacy.reports') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('pharmacy.reports') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-analytics-01 text-xs"></i> Pharmacy Reports</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-package text-xs"></i> Inventory</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-tag-01 text-xs"></i> Categories</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-user-multiple-02 text-xs"></i> Suppliers</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('appointments.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-calendar-03 text-xl text-slate-400"></i>
                    <span>Appointments</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('clinic-rooms.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('clinic-rooms.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-building-03 text-xl text-slate-400"></i>
                    <span>Clinic Rooms</span>
                </a>
            </div>

            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-hr')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-400"></i>
                        <span>Medical Staff / HR</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-hr" class="sidebar-submenu {{ request()->routeIs('users.*','departments.*','shifts.*','admin.doctors.*','admin.queue') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('admin.queue') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('admin.queue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-user-multiple-02 text-xs"></i> Patient Queue</span>
                    </a>
                    <a href="{{ route('admin.doctors.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-activity-01 text-xs"></i> Doctors Management</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-user text-xs"></i> User Accounts</span>
                    </a>
                    <a href="{{ route('departments.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-building-03 text-xs"></i> Departments</span>
                    </a>
                    <a href="{{ route('shifts.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-clock-01 text-xs"></i> Shifts</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('posts.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('posts.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-news-01 text-xl text-slate-400"></i>
                    <span>Blog</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('invoices.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-file-01 text-xl text-slate-400"></i>
                    <span>Invoices</span>
                </a>
            </div>

            <div class="sidebar-group">
                <button type="button" onclick="toggleMenu('menu-reports')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-analytics-02 text-xl text-slate-400"></i>
                        <span>Reports</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-reports" class="sidebar-submenu {{ request()->routeIs('reports.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('reports.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-layout-2-column text-xs"></i> Overview</span>
                    </a>
                    <a href="{{ route('reports.sales') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-money-03 text-xs"></i> Sales Report</span>
                    </a>
                    <a href="{{ route('reports.patients') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.patients') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-user-multiple-02 text-xs"></i> Patient Report</span>
                    </a>
                    <a href="{{ route('reports.doctors') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.doctors') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-activity-01 text-xs"></i> Doctor Performance</span>
                    </a>
                    <a href="{{ route('reports.stock') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-package text-xs"></i> Stock Report</span>
                    </a>
                    <a href="{{ route('reports.revenue') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.revenue') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-analytics-02 text-xs"></i> Revenue Report</span>
                    </a>
                    <a href="{{ route('reports.health') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('reports.health') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-favourite text-xs"></i> System Health</span>
                    </a>
                    <a href="{{ route('manager.reports.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('manager.reports.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-file-02 text-xs"></i> Manager Reports</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-communications')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('notifications.*','sms.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-message-01 text-xl text-slate-400"></i>
                        <span>Communications</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('notifications.*','sms.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-communications" class="sidebar-submenu {{ request()->routeIs('notifications.*','sms.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('notifications.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-sent text-xs"></i> Send Notification</span>
                    </a>
                    <a href="{{ route('notifications.templates') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('notifications.templates') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-file-02 text-xs"></i> Templates</span>
                    </a>
                    <a href="{{ route('sms.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.index','sms.store') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-message-01 text-xs"></i> Send SMS</span>
                    </a>
                    <a href="{{ route('sms.logs') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.logs') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-file-02 text-xs"></i> SMS Logs</span>
                    </a>
                    <a href="{{ route('sms.templates') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('sms.templates') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-copy-01 text-xs"></i> SMS Templates</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('chat.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-message-multiple-02 text-xl text-slate-400"></i>
                    <span>Chat</span>
                    <span id="chatUnreadBadge" class="hidden ml-auto bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">0</span>
                </a>
            </div>

            <div class="sidebar-group">
                <a href="{{ route('activity_logs.index') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}">
                    <i class="hgi-stroke hgi-file-02 text-xl text-slate-400"></i>
                    <span>Audit Logs</span>
                </a>
            </div>

            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-settings')" class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-none text-slate-300 text-sm font-medium {{ request()->routeIs('settings.*','services.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="hgi-stroke hgi-settings-02 text-xl text-slate-400"></i>
                        <span>Settings</span>
                    </div>
                    <i class="hgi-stroke hgi-arrow-down-01 text-xs arrow-icon {{ request()->routeIs('settings.*','services.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div id="menu-settings" class="sidebar-submenu {{ request()->routeIs('settings.*','services.*') ? 'open' : '' }} pl-10 pr-2 space-y-0.5">
                    <a href="{{ route('settings.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-settings-02 text-xs"></i> General</span>
                    </a>
                    <a href="{{ route('settings.email') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.email') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-mail-01 text-xs"></i> Email Config</span>
                    </a>
                    <a href="{{ route('settings.sms') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.sms') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-message-01 text-xs"></i> SMS Gateway</span>
                    </a>
                    <a href="{{ route('settings.payment') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('settings.payment') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-money-03 text-xs"></i> Payment Gateways</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="submenu-item block text-xs text-slate-400 hover:text-white py-1.5 pl-3 {{ request()->routeIs('services.*') ? 'active' : '' }}">
                        <span class="flex items-center gap-2"><i class="hgi-stroke hgi-archive text-xs"></i> Services & Prices</span>
                    </a>
                </div>
            </div>
            @endif

        </div>

        <div class="p-4 border-t border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ ucfirst(Auth::user()->roles->first()->name ?? 'User') }}</p>
                    <p class="text-xs text-slate-500">Account</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('dashboard-logout').submit();" class="text-slate-500 hover:text-white transition-colors">
                    <i class="hgi-stroke hgi-logout-02 text-xl"></i>
                </a>
                <form id="dashboard-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    <div class="lg:ml-64 min-h-screen flex flex-col">

        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <i class="hgi-stroke hgi-menu-01 text-xl"></i>
                </button>
                <h1 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button onclick="document.getElementById('notificationDropdown').classList.toggle('hidden')" class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <i class="hgi-stroke hgi-notification-01 text-xl"></i>
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

        <main class="flex-1 p-6 animate-fade relative">
            @yield('content')
        </main>

    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Shadcn-style pickers: init on focus so dynamically injected inputs also work
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
</body>
</html>
