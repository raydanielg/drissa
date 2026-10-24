@extends('layouts.auth')

@section('title', 'Forgot Password - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md mx-auto" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 overflow-hidden sm:rounded-2xl">
        {{-- Body --}}
        <div class="auth-header px-8 pt-8 pb-6 text-center">
            <div class="auth-logo-entrance auth-logo-glow w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4" style="padding:4px;">
                <i class="hgi-stroke hgi-lock text-4xl text-slate-600"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800 shimmer-text">Forgot Password?</h2>
            <p class="text-gray-500 text-sm mt-1.5">No worries! Enter your email and we'll send you a reset link.</p>
        </div>

        {{-- Body --}}
        <div class="p-8">
            @if (session('status'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-2">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-xl flex-shrink-0 mt-0.5"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-at text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="you@example.com" />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="auth-btn w-full py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 rounded transition-colors flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-mail-01 text-xl"></i>
                    Send Reset Link
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
@endsection
