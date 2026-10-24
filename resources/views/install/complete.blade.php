@extends('install.layout')

@section('title', 'Installation Complete')

@section('content')
<div class="p-8 text-center">
    <div class="w-14 h-14 mx-auto bg-slate-100 ring-1 ring-slate-200 rounded-full flex items-center justify-center mb-5">
        <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-slate-600"></i>
    </div>

    <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Installation Complete</h2>
    <p class="text-sm text-slate-500 mt-1 mb-7">Mfumo wako uko tayari. Ingia na credentials za default hapa chini.</p>

    {{-- Default credentials --}}
    @php
        $accounts = [
            ['Admin', 'admin@drissa.test', 'Full access'],
            ['Reception', 'reception@drissa.test', 'Front desk'],
            ['Doctor', 'doctor@drissa.test', 'Consultations'],
            ['Lab', 'lab@drissa.test', 'Laboratory'],
            ['Pharmacy', 'pharmacy@drissa.test', 'Dispensing'],
            ['Ultrasound', 'ultrasound@drissa.test', 'Radiology'],
        ];
    @endphp
    <div class="text-left rounded-md ring-1 ring-gray-200 overflow-hidden mb-5">
        <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Default Accounts</h3>
            <span class="text-[11px] font-semibold text-gray-400">password: <code class="text-slate-700 font-bold">password</code></span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($accounts as [$role, $email, $desc])
                <div class="flex items-center justify-between px-4 py-2.5 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded bg-slate-100 text-slate-700 flex items-center justify-center text-[11px] font-bold">{{ strtoupper(substr($role, 0, 1)) }}</div>
                        <div>
                            <p class="text-[13px] font-semibold text-slate-700 leading-none">{{ $role }}</p>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $email }}</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-medium text-slate-400">{{ $desc }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-start gap-2.5 bg-white border-l-2 border-amber-500 rounded-md px-4 py-3 mb-6 text-left ring-1 ring-gray-200">
        <i class="hgi-stroke hgi-alert-02 text-base text-amber-500 shrink-0 mt-0.5"></i>
        <p class="text-xs text-amber-700 font-medium">Badili passwords hizi baada ya kuingia mara ya kwanza kwa usalama.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 h-11 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded transition-all">
            Staff Login
            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
        </a>
        <a href="{{ route('public.home') }}" class="flex items-center justify-center gap-2 h-11 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-semibold rounded transition-all">
            Go to Website
        </a>
    </div>
</div>
@endsection
