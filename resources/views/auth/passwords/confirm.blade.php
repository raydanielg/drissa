@extends('layouts.auth')

@section('title', 'Confirm Password - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-800 px-8 py-8 text-center">
            <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-sm rounded-md flex items-center justify-center mb-4">
                <img src="{{ asset('forgotpass.png') }}" alt="Confirm Password" class="w-10 h-10 object-contain">
            </div>
            <h2 class="text-xl font-bold text-white">Confirm Password</h2>
            <p class="text-slate-400 text-xs mt-1">Please confirm your password before continuing</p>
        </div>

        <div class="p-8">
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-lock text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="Enter your password" />
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 rounded transition-colors flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-xl"></i>
                    Confirm Password
                </button>
            </form>

            @if (Route::has('password.request'))
                <p class="mt-6 text-center text-sm text-gray-500">
                    <a href="{{ route('password.request') }}" class="font-semibold text-slate-600 hover:text-slate-800 transition-colors">Forgot your password?</a>
                </p>
            @endif
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
