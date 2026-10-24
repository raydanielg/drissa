@extends('layouts.auth')

@section('title', 'Reset Password - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md mx-auto" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 overflow-hidden sm:rounded-2xl">
        <script>
    function togglePassword(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        const eyeOff = document.getElementById(eyeOffId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.add('hidden');
            eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            eye.classList.remove('hidden');
            eyeOff.classList.add('hidden');
        }
    }

    function checkStrength(password) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^A-Za-z0-9]/)) score++;

        const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
        const colors = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const labels = ['Weak', 'Weak', 'Fair', 'Fair', 'Strong', 'Strong'];

        bar.style.width = widths[score];
        bar.style.background = colors[score];
        label.textContent = labels[score];
        label.style.color = colors[score];
    }
</script>
        <div class="auth-header px-8 pt-8 pb-6 text-center">
            <div class="auth-logo-entrance auth-logo-glow w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4" style="padding:4px;">
                <i class="hgi-stroke hgi-refresh text-4xl text-slate-600"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800 shimmer-text">Reset Password</h2>
            <p class="text-gray-500 text-sm mt-1.5">Create a new secure password for your account</p>
        </div>

        <script>
    function togglePassword(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        const eyeOff = document.getElementById(eyeOffId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.add('hidden');
            eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            eye.classList.remove('hidden');
            eyeOff.classList.add('hidden');
        }
    }

    function checkStrength(password) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^A-Za-z0-9]/)) score++;

        const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
        const colors = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const labels = ['Weak', 'Weak', 'Fair', 'Fair', 'Strong', 'Strong'];

        bar.style.width = widths[score];
        bar.style.background = colors[score];
        label.textContent = labels[score];
        label.style.color = colors[score];
    }
</script>
        <div class="p-8">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5" id="resetForm">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-at text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="you@example.com" />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-lock text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password" type="password" name="password" required autocomplete="new-password" minlength="8" oninput="checkStrength(this.value)"
                            class="w-full pl-11 pr-11 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="Min. 8 characters" />
                        <button type="button" onclick="togglePassword('password','eyeIcon','eyeOffIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-slate-600 transition-colors">
                            <i class="hgi-stroke hgi-view text-xl"></i>
                            <i class="hgi-stroke hgi-view-off text-xl hidden"></i>
                        </button>
                    </div>
                    <script>
    function togglePassword(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        const eyeOff = document.getElementById(eyeOffId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.add('hidden');
            eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            eye.classList.remove('hidden');
            eyeOff.classList.add('hidden');
        }
    }

    function checkStrength(password) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^A-Za-z0-9]/)) score++;

        const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
        const colors = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const labels = ['Weak', 'Weak', 'Fair', 'Fair', 'Strong', 'Strong'];

        bar.style.width = widths[score];
        bar.style.background = colors[score];
        label.textContent = labels[score];
        label.style.color = colors[score];
    }
</script>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full transition-all duration-300" style="width:0%; background:#ef4444;"></div>
                        </div>
                        <span id="strengthLabel" class="text-xs font-medium text-gray-400 w-16 text-right">Weak</span>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-checkmark-circle-02 text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="Re-enter your password" />
                    </div>
                </div>

                <button type="submit" class="auth-btn w-full py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 rounded transition-colors flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-refresh text-xl"></i>
                    Reset Password
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                    <i class="hgi-stroke hgi-arrow-left-02 text-base"></i>
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400/70">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>

<script>
    function togglePassword(inputId, eyeId, eyeOffId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        const eyeOff = document.getElementById(eyeOffId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.classList.add('hidden');
            eyeOff.classList.remove('hidden');
        } else {
            input.type = 'password';
            eye.classList.remove('hidden');
            eyeOff.classList.add('hidden');
        }
    }

    function checkStrength(password) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[a-z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^A-Za-z0-9]/)) score++;

        const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
        const colors = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
        const labels = ['Weak', 'Weak', 'Fair', 'Fair', 'Strong', 'Strong'];

        bar.style.width = widths[score];
        bar.style.background = colors[score];
        label.textContent = labels[score];
        label.style.color = colors[score];
    }
</script>
@endsection
