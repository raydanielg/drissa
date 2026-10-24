@extends('install.layout')

@section('title', 'Requirements')

@section('content')
<div class="p-8">
    <h2 class="text-lg font-extrabold tracking-tight text-slate-900">System Requirements</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Kuangalia kama server yako iko tayari kwa installation.</p>

    @php
        $rows = array_merge(
            [['PHP ' . $phpVersion . ' (8.2+ required)', version_compare(PHP_VERSION, '8.2.0', '>=')]],
            collect($extensions)->map(fn($ok, $ext) => [$ext, $ok])->values()->all(),
            collect($writablePaths)->map(fn($ok, $path) => ['Writable: ' . $path, $ok])->values()->all(),
        );
    @endphp

    <div class="divide-y divide-slate-100 rounded-md ring-1 ring-gray-200 overflow-hidden mb-6">
        @foreach($rows as [$label, $ok])
            <div class="flex items-center justify-between px-4 py-2.5 bg-white odd:bg-slate-50/60">
                <span class="text-[13px] font-medium text-slate-600">{{ $label }}</span>
                @if($ok)
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                        <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                        PASS
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-500">
                        <i class="hgi-stroke hgi-cancel-01 text-sm"></i>
                        FAIL
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    @if($allGood)
        <a href="{{ route('install.database') }}" class="w-full flex items-center justify-center gap-2 h-11 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded transition-all">
            Continue
            <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
        </a>
    @else
        <div class="flex items-start gap-3 bg-white border-l-2 border-red-500 rounded-md p-4 mb-4 ring-1 ring-gray-200">
            <i class="hgi-stroke hgi-alert-circle text-xl text-red-500 shrink-0 mt-0.5"></i>
            <p class="text-[13px] text-red-700 font-medium">Baadhi ya requirements hazijakamilika. Rekebisha matatizo hapo juu kisha refresh page hii.</p>
        </div>
        <button onclick="location.reload()" class="w-full flex items-center justify-center gap-2 h-11 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-bold rounded transition-all">
            <i class="hgi-stroke hgi-refresh text-base"></i>
            Re-check
        </button>
    @endif
</div>
@endsection
