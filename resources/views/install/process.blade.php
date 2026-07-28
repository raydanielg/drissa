@extends('install.layout')

@section('title', 'Installing')

@section('content')
<div class="p-8">
    <h2 class="text-xl font-extrabold text-emerald-900 mb-2">Running Installation</h2>
    <p class="text-sm text-gray-500 mb-6">Migrating database tables and seeding initial data. Please do not close this page.</p>

    {{-- Status --}}
    <div id="statusBox" class="bg-emerald-50 rounded-xl p-4 mb-4">
        <div class="flex items-center gap-3">
            <div id="spinner" class="w-6 h-6 border-3 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
            <p id="statusText" class="text-sm font-semibold text-emerald-800">Preparing installation...</p>
        </div>
    </div>

    {{-- Output log --}}
    <div class="bg-gray-900 rounded-xl p-4 max-h-64 overflow-y-auto mb-4">
        <pre id="outputLog" class="text-xs text-gray-300 font-mono whitespace-pre-wrap">$ waiting for installation to start...</pre>
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
        <div id="progressBar" class="bg-gradient-to-r from-gold-400 to-gold-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
    </div>

    {{-- Result (hidden initially) --}}
    <div id="resultBox" class="hidden">
        <div id="successBox" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold text-emerald-800">Installation Complete!</p>
                    <p class="text-sm text-emerald-600">Database migrated and seeded successfully.</p>
                </div>
            </div>
            <a href="{{ route('install.complete') }}" class="mt-4 w-full flex items-center justify-center gap-2 bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                Continue
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div id="errorBox" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="font-bold text-red-800">Installation Failed</p>
                    <p id="errorMessage" class="text-sm text-red-600"></p>
                </div>
            </div>
            <button onclick="runInstall()" class="mt-4 w-full flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Try Again
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const progressSteps = [
        { pct: 15, text: 'Connecting to database...' },
        { pct: 30, text: 'Running migrations...' },
        { pct: 60, text: 'Creating database tables...' },
        { pct: 80, text: 'Seeding initial data...' },
        { pct: 95, text: 'Finalizing installation...' },
    ];
    let stepIndex = 0;

    function updateProgress() {
        if (stepIndex < progressSteps.length) {
            const step = progressSteps[stepIndex];
            document.getElementById('progressBar').style.width = step.pct + '%';
            document.getElementById('statusText').textContent = step.text;
            stepIndex++;
            setTimeout(updateProgress, 800);
        }
    }

    function appendLog(text) {
        const log = document.getElementById('outputLog');
        log.textContent += '\n' + text;
        log.parentElement.scrollTop = log.parentElement.scrollHeight;
    }

    function runInstall() {
        document.getElementById('resultBox').classList.add('hidden');
        document.getElementById('successBox').classList.add('hidden');
        document.getElementById('errorBox').classList.add('hidden');
        document.getElementById('spinner').style.display = 'block';
        document.getElementById('statusText').textContent = 'Preparing installation...';
        document.getElementById('progressBar').style.width = '0%';
        stepIndex = 0;
        document.getElementById('outputLog').textContent = '$ starting installation...';

        updateProgress();

        fetch('{{ route("install.run") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('resultBox').classList.remove('hidden');

            if (data.status === 'success') {
                document.getElementById('statusText').textContent = 'Installation complete!';
                if (data.output) {
                    appendLog(data.output);
                }
                document.getElementById('successBox').classList.remove('hidden');
            } else if (data.status === 'already_installed') {
                document.getElementById('statusText').textContent = 'Already installed.';
                window.location.href = '{{ route("install.complete") }}';
            } else {
                document.getElementById('statusText').textContent = 'Installation failed.';
                document.getElementById('errorMessage').textContent = data.message || 'Unknown error occurred.';
                document.getElementById('errorBox').classList.remove('hidden');
            }
        })
        .catch(err => {
            document.getElementById('progressBar').style.width = '100%';
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('resultBox').classList.remove('hidden');
            document.getElementById('statusText').textContent = 'Installation failed.';
            document.getElementById('errorMessage').textContent = 'Network error: ' + err.message;
            document.getElementById('errorBox').classList.remove('hidden');
        });
    }

    document.addEventListener('DOMContentLoaded', runInstall);
</script>
@endpush
@endsection
