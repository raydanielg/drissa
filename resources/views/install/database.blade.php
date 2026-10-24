@extends('install.layout')

@section('title', 'Database Configuration')

@section('content')
<div class="p-8">
    <h2 class="text-lg font-extrabold tracking-tight text-slate-900">Database Configuration</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Ingiza MySQL credentials. Database itatengenezwa automatic kisha haijapo.</p>

    @if(session('error'))
        <div class="flex items-start gap-3 bg-white border-l-2 border-red-500 rounded-md p-4 mb-5 ring-1 ring-gray-200">
            <i class="hgi-stroke hgi-alert-circle text-xl text-red-500 shrink-0 mt-0.5"></i>
            <p class="text-[13px] text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('install.process') }}" class="space-y-5">
        @csrf

        {{-- Database Settings --}}
        <div class="rounded-md ring-1 ring-gray-200 p-4 space-y-3">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Application</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">App Name</label>
                    <x-ui.input type="text" name="app_name" value="Uzazi Clinic" class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">App URL</label>
                    <x-ui.input type="text" name="app_url" value="{{ request()->getSchemeAndHttpHost() }}" class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
            </div>
        </div>

        {{-- Database Settings --}}
        <div class="rounded-md ring-1 ring-gray-200 p-4 space-y-3">
            <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">MySQL Database</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Host <span class="text-red-400">*</span></label>
                    <x-ui.input type="text" name="db_host" value="127.0.0.1" required class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Port <span class="text-red-400">*</span></label>
                    <x-ui.input type="text" name="db_port" value="3306" required class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Database Name <span class="text-red-400">*</span></label>
                <x-ui.input type="text" name="db_name" value="drissa_clinic" required class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Username <span class="text-red-400">*</span></label>
                    <x-ui.input type="text" name="db_user" value="root" required class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
                    <x-ui.input type="password" name="db_pass" value="" placeholder="Optional" class="w-full h-10 px-3 rounded-lg border-0 ring-1 ring-slate-200 text-sm placeholder:text-slate-300 focus:ring-2 focus:ring-slate-300 outline-none transition-shadow" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-1">
            <a href="{{ route('install.welcome') }}" class="flex items-center justify-center h-11 px-5 rounded border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-semibold transition-all">
                &larr; Back
            </a>
            <button type="submit" class="flex-1 flex items-center justify-center gap-2 h-11 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded transition-all">
                Test Connection &amp; Install
                <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
            </button>
        </div>
    </form>
</div>
@endsection
