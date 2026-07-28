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
        .auth-card-entrance { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-header-entrance { animation: fadeInDown 0.6s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
        .auth-field-entrance { animation: fadeIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .auth-btn:hover { animation: pulse 0.8s ease-in-out infinite; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .page-transition { animation: simpleFadeIn 0.35s ease-out both; }
        #particleCanvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; }
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

    // Animate.css entrance animations for auth card
    (function() {
        const card = document.querySelector('#authMain .max-w-md');
        if (!card) return;

        card.style.animation = 'none';
        card.classList.add('auth-card-entrance');

        const header = card.querySelector('.auth-header');
        if (header) header.classList.add('auth-header-entrance');

        const fields = card.querySelectorAll('form > div, form > button');
        fields.forEach((field, i) => {
            field.classList.add('auth-field-entrance');
            field.style.animationDelay = (0.3 + (i * 0.08)) + 's';
        });

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
