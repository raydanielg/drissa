@extends('layouts.auth')

@section('title', 'Register - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Login link --}}
        <div class="bg-slate-800 px-8 py-8 text-center">
            <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-sm rounded-md flex items-center justify-center mb-4">
                <img src="{{ asset('register.png') }}" alt="Register" class="w-10 h-10 object-contain">
            </div>
            <h2 class="text-xl font-bold text-white">Create Account</h2>
            <p class="text-slate-400 text-xs mt-1">Get started with {{ config('app.name', 'Laravel') }}</p>
        </div>

        {{-- Login link --}}
        <div class="p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Login link --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-user text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('name') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="John Doe" />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Login link --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="hgi-stroke hgi-at text-xl text-gray-400"></i>
                            </div>
                            <x-ui.input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                                placeholder="name@example.com" />
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Login link --}}
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="hgi-stroke hgi-call-02 text-xl text-gray-400"></i>
                            </div>
                            <x-ui.input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                                class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('phone') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                                placeholder="+255 7XX XXX XXX" />
                        </div>
                        @error('phone')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="role" value="customer">

                {{-- Login link --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-lock text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password" type="password" name="password" required autocomplete="new-password" minlength="8"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="Min. 8 characters" />
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><i class="hgi-stroke hgi-alert-circle text-base flex-shrink-0"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Login link --}}
                <div>
                    <label for="password-confirm" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-checkmark-circle-02 text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-slate-300 outline-none transition-all text-sm"
                            placeholder="Re-enter your password" />
                    </div>
                </div>

                {{-- Login link --}}
                <button type="submit" class="w-full py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 rounded transition-colors flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-user-add-01 text-xl"></i>
                    Create Account Securely
                </button>
            </form>

            {{-- Login link --}}
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">or</span></div>
            </div>

            {{-- Login link --}}
            <p class="text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-slate-800 transition-colors">Sign in</a>
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
