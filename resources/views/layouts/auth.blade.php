<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel') . ' — ' . __('Authentication'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <style>
        @keyframes simpleFadeIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

        /* 3D Card Entrance */
        @keyframes cardEntrance {
            0%   { opacity:0; transform: perspective(1200px) rotateX(-12deg) translateY(60px) scale(0.92); }
            50%  { opacity:0.6; transform: perspective(1200px) rotateX(-4deg) translateY(20px) scale(0.97); }
            100% { opacity:1; transform: perspective(1200px) rotateX(0deg) translateY(0) scale(1); }
        }
        .auth-card-entrance { animation: cardEntrance 1s cubic-bezier(0.16,1,0.3,1) both; }

        /* Header Slide + Glow */
        @keyframes headerEntrance {
            0%   { opacity:0; transform: translateY(-24px); filter: blur(6px); }
            60%  { opacity:0.8; filter: blur(2px); }
            100% { opacity:1; transform: translateY(0); filter: blur(0); }
        }
        .auth-header-entrance { animation: headerEntrance 0.8s cubic-bezier(0.16,1,0.3,1) 0.3s both; }

        /* Logo Bounce + Glow Ring */
        @keyframes logoEntrance {
            0%   { opacity:0; transform: scale(0) rotate(-180deg); }
            60%  { opacity:1; transform: scale(1.15) rotate(10deg); }
            100% { opacity:1; transform: scale(1) rotate(0deg); }
        }
        .auth-logo-entrance { animation: logoEntrance 0.9s cubic-bezier(0.34,1.56,0.64,1) 0.4s both; }
        @keyframes logoGlow {
            0%,100% { box-shadow: 0 0 0 0 rgba(77,181,168,0); }
            50%     { box-shadow: 0 0 25px 4px rgba(77,181,168,0.35); }
        }
        .auth-logo-glow { animation: logoGlow 3s ease-in-out 1.3s infinite; border-radius: 50%; }

        /* Field Staggered Slide-In */
        @keyframes fieldSlideIn {
            0%   { opacity:0; transform: translateX(-30px); filter: blur(4px); }
            100% { opacity:1; transform: translateX(0); filter: blur(0); }
        }
        .auth-field-entrance { animation: fieldSlideIn 0.6s cubic-bezier(0.16,1,0.3,1) both; }

        /* Button Glow Pulse on Hover */
        @keyframes btnGlow {
            0%,100% { box-shadow: 0 4px 14px rgba(249,172,0,0.3); }
            50%     { box-shadow: 0 6px 24px rgba(249,172,0,0.55); }
        }
        .auth-btn:hover { animation: btnGlow 1.2s ease-in-out infinite; transform: translateY(-1px); }

        /* Floating Card */
        @keyframes floatCard {
            0%,100% { transform: translateY(0); }
            50%     { transform: translateY(-6px); }
        }
        .auth-card-float { animation: floatCard 4s ease-in-out 2s infinite; }

        /* Shimmer on header text */
        @keyframes shimmerText {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .shimmer-text {
            background: linear-gradient(90deg, #1f2937 0%, #1f2937 40%, #024938 50%, #1f2937 60%, #1f2937 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmerText 3s linear 1.5s infinite;
        }

        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        #particleCanvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }

        /* Card glow border */
        @keyframes borderGlow {
            0%,100% { border-color: rgba(77,181,168,0.15); }
            50%     { border-color: rgba(77,181,168,0.4); }
        }
        .auth-card-glow { animation: borderGlow 4s ease-in-out 1.5s infinite; }

        /* Background Clinic Name Watermark */
        .bg-clinic-name {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
            white-space: nowrap;
            font-size: clamp(3rem, 12vw, 9rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(77, 181, 168, 0.07);
            text-stroke: 1.5px rgba(77, 181, 168, 0.07);
            user-select: none;
            animation: bgNameFade 3s ease-out 0.5s both, bgNameFloat 8s ease-in-out 3s infinite;
        }
        @keyframes bgNameFade {
            0%   { opacity: 0; transform: translate(-50%, -50%) scale(1.1); }
            100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        @keyframes bgNameFloat {
            0%,100% { transform: translate(-50%, -50%) scale(1); }
            50%     { transform: translate(-50%, -52%) scale(1.02); }
        }
        .bg-clinic-sub {
            position: fixed;
            top: calc(50% + clamp(2rem, 7vw, 5rem));
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            pointer-events: none;
            white-space: nowrap;
            font-size: clamp(0.7rem, 2vw, 1.1rem);
            font-weight: 600;
            letter-spacing: 0.5em;
            text-transform: uppercase;
            color: rgba(77, 181, 168, 0.08);
            user-select: none;
            animation: bgNameFade 3s ease-out 1s both;
        }
    </style>
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
</head>
<body class="font-['Nunito',sans-serif] antialiased text-slate-800 min-h-screen" style="background: linear-gradient(135deg, #001816 0%, #01241f 30%, #013028 60%, #024938 100%);">

    <canvas id="particleCanvas"></canvas>

    {{-- Background Clinic Name Watermark --}}
    <div class="bg-clinic-name">{{ config('app.name', 'Uzazi Clinic') }}</div>
    <div class="bg-clinic-sub">Clinic &middot; Dar es Salaam</div>

    <div id="ajaxLoader" class="ajax-loader"></div>

    <main id="authMain" class="relative z-10 min-h-screen w-full flex items-center justify-center p-4 sm:p-6">
        @yield('content')
    </main>

    <script>
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

    // Button loading state on all auth forms
    (function() {
        const authMain = document.getElementById('authMain');
        if (!authMain) return;

        authMain.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;

                btn.disabled = true;
                const original = btn.innerHTML;
                btn.setAttribute('data-original', original);
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-900 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
                btn.classList.add('cursor-not-allowed', 'opacity-90');
            });
        });
    })();

    // Entrance animations for auth card
    (function() {
        const card = document.querySelector('#authMain .max-w-md > div');
        if (!card) return;

        // 3D card entrance + float + border glow (combined animations)
        card.style.animation = 'none';
        void card.offsetWidth; // reflow
        card.style.animation = "cardEntrance 1s cubic-bezier(0.16,1,0.3,1) both, floatCard 4s ease-in-out 2s infinite, borderGlow 4s ease-in-out 1.5s infinite";

        // Logo entrance + glow
        const logo = card.querySelector('img');
        if (logo) {
            logo.classList.add('auth-logo-entrance', 'auth-logo-glow');
            logo.style.padding = '4px';
        }

        // Header entrance
        const header = card.querySelector('.auth-header');
        if (header) header.classList.add('auth-header-entrance');

        // Shimmer on header title
        const title = card.querySelector('.auth-header h2');
        if (title) title.classList.add('shimmer-text');

        // Staggered field entrance
        const fields = card.querySelectorAll('form > div, form > button');
        fields.forEach((field, i) => {
            field.classList.add('auth-field-entrance');
            field.style.animationDelay = (0.5 + (i * 0.1)) + 's';
        });

        // Button glow on hover
        const submitBtn = card.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.classList.add('auth-btn');
    })();

    // Particle Network Animation
    (function() {
        const canvas = document.getElementById('particleCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let particles = [];
        let mouse = { x: null, y: null, radius: 140 };
        let w, h;

        function resize() {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        window.addEventListener('mousemove', function(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });
        window.addEventListener('mouseout', function() {
            mouse.x = null;
            mouse.y = null;
        });

        const count = Math.min(Math.floor((w * h) / 14000), 110);

        class Particle {
            constructor() {
                this.x = Math.random() * w;
                this.y = Math.random() * h;
                this.vx = (Math.random() - 0.5) * 0.5;
                this.vy = (Math.random() - 0.5) * 0.5;
                this.size = Math.random() * 2 + 1;
                this.baseSize = this.size;
                this.opacity = Math.random() * 0.4 + 0.3;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > w) this.vx *= -1;
                if (this.y < 0 || this.y > h) this.vy *= -1;
                if (mouse.x !== null) {
                    const dx = this.x - mouse.x;
                    const dy = this.y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < mouse.radius) {
                        const force = (mouse.radius - dist) / mouse.radius;
                        this.x += (dx / dist) * force * 2.5;
                        this.y += (dy / dist) * force * 2.5;
                        this.size = this.baseSize + force * 2;
                    } else {
                        this.size = this.baseSize;
                    }
                } else {
                    this.size = this.baseSize;
                }
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(77, 181, 168, ' + this.opacity + ')';
                ctx.fill();
            }
        }

        function init() {
            particles = [];
            for (let i = 0; i < count; i++) {
                particles.push(new Particle());
            }
        }

        function connect() {
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 130) {
                        const alpha = (1 - dist / 130) * 0.25;
                        ctx.strokeStyle = 'rgba(77, 181, 168, ' + alpha + ')';
                        ctx.lineWidth = 0.6;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
                if (mouse.x !== null) {
                    const dx = particles[i].x - mouse.x;
                    const dy = particles[i].y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < mouse.radius) {
                        const alpha = (1 - dist / mouse.radius) * 0.5;
                        ctx.strokeStyle = 'rgba(249, 172, 0, ' + alpha + ')';
                        ctx.lineWidth = 0.8;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, w, h);
            particles.forEach(p => { p.update(); p.draw(); });
            connect();
            requestAnimationFrame(animate);
        }

        init();
        animate();
    })();
    </script>
</body>
</html>
