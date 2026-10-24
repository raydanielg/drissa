@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name', 'Uzazi Clinic'))

@section('content')
<div class="w-full max-w-sm fade-up">
    <div class="bg-white rounded-md border border-gray-200 shadow-xl overflow-hidden">

        <div class="px-7 py-8">
            <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
            <h2 class="text-lg font-bold text-center text-slate-900 mb-7">Welcome Back</h2>

            <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf

                <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-mail-01 text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full h-11 pl-10 pr-3 rounded-md border @error('email') border-red-300 @else border-gray-200 @enderror bg-gray-50 focus:bg-white focus:ring-2 focus:ring-slate-300 focus:border-transparent outline-none transition text-sm placeholder:text-gray-400"
                            placeholder="you@example.com" />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
                <div>
                    <label for="password" class="block text-xs font-medium text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-lock text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full h-11 pl-10 pr-11 rounded-md border @error('password') border-red-300 @else border-gray-200 @enderror bg-gray-50 focus:bg-white focus:ring-2 focus:ring-slate-300 focus:border-transparent outline-none transition text-sm placeholder:text-gray-400"
                            placeholder="Enter your password" />
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-slate-600 transition-colors" aria-label="Show password">
                            <i class="hgi-stroke hgi-view text-xl"></i>
                            <i class="hgi-stroke hgi-view-off text-xl hidden"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-3.5 h-3.5 rounded border-gray-300 text-slate-800 focus:ring-slate-300">
                        <span class="text-xs text-gray-600">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-slate-700 hover:text-slate-900 transition-colors">Forgot password?</a>
                    @endif
                </div>

                <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
                <button type="submit" class="w-full h-11 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-md transition-colors flex items-center justify-center gap-2.5">
                    <i class="hgi-stroke hgi-logout-03 text-xl"></i>
                    Sign In
                </button>
            </form>

            <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
            <div class="mt-6 space-y-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full h-11 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-md hover:bg-gray-50 transition-colors flex items-center justify-center gap-2.5">
                        <i class="hgi-stroke hgi-user-add-01 text-xl text-gray-400"></i>
                        Create an account
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeOff = document.getElementById('eyeOffIcon');
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
</script>
@endsection
