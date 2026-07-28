@extends('install.layout')

@section('title', 'Installation Complete')

@section('content')
<div class="p-8 text-center">
    <div class="w-20 h-20 mx-auto bg-emerald-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>

    <h2 class="text-2xl font-extrabold text-emerald-900 mb-2">Installation Complete!</h2>
    <p class="text-sm text-gray-500 mb-8">Your clinic management system is ready to use. You can now log in with the default credentials below.</p>

    {{-- Default credentials --}}
    <div class="bg-gray-50 rounded-xl p-5 mb-6 text-left">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Default Login Credentials</h3>
        <div class="space-y-2">
            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-700">Admin</p>
                    <p class="text-xs text-gray-500">admin@drissa.test / password</p>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">Full Access</span>
            </div>
            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-700">Doctor</p>
                    <p class="text-xs text-gray-500">doctor@drissa.test / password</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Doctor</span>
            </div>
            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-700">Reception</p>
                    <p class="text-xs text-gray-500">reception@drissa.test / password</p>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded">Reception</span>
            </div>
            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-700">Lab</p>
                    <p class="text-xs text-gray-500">lab@drissa.test / password</p>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">Lab</span>
            </div>
            <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-700">Pharmacy</p>
                    <p class="text-xs text-gray-500">pharmacy@drissa.test / password</p>
                </div>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded">Pharmacy</span>
            </div>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
        <p class="text-xs text-amber-700 font-semibold">⚠ Please change these passwords after your first login for security.</p>
    </div>

    <a href="{{ route('public.home') }}" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
        Go to Website
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <a href="{{ route('login') }}" class="mt-3 w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
        Staff Login
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
    </a>
</div>
@endsection
