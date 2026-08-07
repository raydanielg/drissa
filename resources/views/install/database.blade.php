@extends('install.layout')

@section('title', 'Database Configuration')

@section('content')
<div class="p-8">
    <h2 class="text-xl font-extrabold text-emerald-900 mb-2">Database Configuration</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your MySQL database credentials. The database will be created automatically if it doesn't exist.</p>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('install.process') }}" class="space-y-4">
        @csrf

        {{-- App Settings --}}
        <div class="bg-emerald-50 rounded-xl p-4 space-y-4">
            <h3 class="text-sm font-bold text-emerald-800 uppercase tracking-wider">Application Settings</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">App Name</label>
                    <input type="text" name="app_name" value="Uzazi Clinic" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">App URL</label>
                    <input type="text" name="app_url" value="{{ request()->getSchemeAndHttpHost() }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
        </div>

        {{-- Database Settings --}}
        <div class="bg-gray-50 rounded-xl p-4 space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Database Settings</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">DB Host *</label>
                    <input type="text" name="db_host" value="127.0.0.1" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">DB Port *</label>
                    <input type="text" name="db_port" value="3306" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Database Name *</label>
                <input type="text" name="db_name" value="drissa_clinic" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">DB Username *</label>
                    <input type="text" name="db_user" value="root" required class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">DB Password</label>
                    <input type="password" name="db_pass" value="" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('install.welcome') }}" class="px-5 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm transition-all">
                &larr; Back
            </a>
            <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                Test & Install
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </form>
</div>
@endsection
