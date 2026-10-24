<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel') . ' — ' . __('Authentication'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @vite(['resources/css/app.css'])
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <style>
        @keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .5s cubic-bezier(.16,1,.3,1) both; }

        /* Compact app toasts — applies to every Swal toast */
        .swal2-popup.swal2-toast { padding: 10px 14px !important; border-radius: 8px !important; border: 1px solid #e5e7eb !important; box-shadow: 0 4px 16px rgba(15,23,42,.12) !important; max-width: 340px; animation-duration: .2s; }
        .swal2-popup.swal2-toast .swal2-title { font-size: 12.5px !important; font-weight: 500 !important; color: #1f2937 !important; padding: 0 !important; margin: 0 !important; text-align: left; line-height: 1.45; }
        .swal2-popup.swal2-toast .swal2-icon { width: 18px !important; height: 18px !important; min-width: 18px; margin: 0 10px 0 0 !important; border-width: 2px !important; }
        .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content { font-size: 11px !important; font-weight: 700; }
        .swal2-popup.swal2-toast .swal2-close { font-size: 14px !important; width: 18px !important; height: 18px !important; color: #9ca3af !important; padding: 0 !important; margin: -2px -4px 0 8px !important; }
        .swal2-popup.swal2-toast .swal2-close:hover { color: #4b5563 !important; }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar { height: 2px !important; background: #cbd5e1 !important; }
    </style>
</head>
<body class="antialiased text-slate-800 min-h-screen" style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">

    <script>
    // Shared compact toast
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

    (function() {
        @if(session('status'))
            window.AppToast.fire({ icon: 'success', title: @json(session('status')) });
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

    // Submit button loading state
    (function() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Signing in...';
                btn.classList.add('cursor-not-allowed', 'opacity-80');
            });
        });
    })();
    </script>
    <           btn.innerHTML = '<svg class="animate-spin mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Signing in...';
                btn.classList.add('cursor-not-allowed', 'opacity-80');
            });
        });
    })();
    </script>

    <a href="{{ Route::has('public.home') ? route('public.home') : url('/') }}" class="fixed top-6 left-6 z-20">
        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Uzazi Clinic') }}" class="w-11 h-11 rounded-md object-cover">
    </a>

    <main class="relative z-10 min-h-screen w-full flex items-center justify-center lg:justify-end p-4 sm:p-8 lg:pr-16 xl:pr-24">
        @yield('content')
    </main>

    <p class="fixed bottom-4 left-6 z-10 text-[11px] text-white/40">&copy; {{ date('Y') }} {{ config('app.name', 'Uzazi Clinic') }}. All rights reserved.</p>

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
    @stack('scripts')
</body>
</html>
