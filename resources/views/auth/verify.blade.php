@extends('layouts.auth')

@section('title', 'Verify Email - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Content --}}
        <div class="bg-slate-800 px-8 py-8 text-center">
            <div class="w-16 h-16 mx-auto bg-white/10 backdrop-blur-sm rounded-md flex items-center justify-center mb-4">
                <img src="{{ asset('verfyottp.png') }}" alt="Verify" class="w-10 h-10 object-contain">
            </div>
            <h2 class="text-xl font-bold text-white">Verify Email</h2>
            <p class="text-slate-400 text-xs mt-1">One more step to get started</p>
        </div>

        {{-- Content --}}
        <div class="p-8 text-center">
            @if (session('resent'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-xl flex-shrink-0"></i>
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-5">
                <i class="hgi-stroke hgi-sent text-4xl text-slate-500"></i>
            </div>

            <p class="text-gray-600 mb-2">Before proceeding, please check your email for a verification link.</p>
            <p class="text-gray-500 text-sm mb-6">If you did not receive the email, click the button below to request another.</p>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full py-3 text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 rounded transition-colors flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-refresh text-xl"></i>
                    Resend Verification Link
                </button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
