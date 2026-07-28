@extends('install.layout')

@section('title', 'Requirements')

@section('content')
<div class="p-8">
    <h2 class="text-xl font-extrabold text-emerald-900 mb-2">System Requirements</h2>
    <p class="text-sm text-gray-500 mb-6">Let's make sure your server is ready for installation.</p>

    {{-- PHP Version --}}
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">PHP Version</h3>
        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
            <span class="text-sm font-semibold text-gray-700">PHP {{ $phpVersion }}</span>
            @if(version_compare(PHP_VERSION, '8.2.0', '>='))
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">PASS</span>
            @else
                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">FAIL (8.2+ required)</span>
            @endif
        </div>
    </div>

    {{-- Extensions --}}
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">PHP Extensions</h3>
        <div class="space-y-2">
            @foreach($extensions as $ext => $loaded)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                    <span class="text-sm font-semibold text-gray-700">{{ $ext }}</span>
                    @if($loaded)
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">PASS</span>
                    @else
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">FAIL</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Writable Paths --}}
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">Writable Paths</h3>
        <div class="space-y-2">
            @foreach($writablePaths as $path => $writable)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                    <span class="text-sm font-semibold text-gray-700">{{ $path }}</span>
                    @if($writable)
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">PASS</span>
                    @else
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">FAIL</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    @if($allGood)
        <a href="{{ route('install.database') }}" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
            Continue
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-red-700 font-semibold">Some requirements are not met. Please fix the issues above and refresh this page.</p>
        </div>
        <button onclick="location.reload()" class="w-full flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Re-check
        </button>
    @endif
</div>
@endsection
