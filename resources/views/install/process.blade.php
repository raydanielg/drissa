@extends('install.layout')

@section('title', 'Installing')

@section('content')
<div class="p-8">
    <h2 class="text-lg font-extrabold tracking-tight text-slate-900">Installing</h2>
    <p class="text-sm text-slate-500 mt-1 mb-6">Tables na data za awali zinaandaliwa. Usifunge page hii.</p>

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
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch(e) {
                        throw new Error('Server returned HTML instead of JSON (HTTP ' + res.status + '). Check if migration completed successfully.');
                    }
                });
            }
            return res.json();
        })
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
    <div class="flex items-center gap-3 bg-white border-l-2 border-slate-400 rounded-md px-4 py-3 mb-4 ring-1 ring-gray-200">
        <svg id="spinner" class="w-5 h-5 text-slate-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/></svg>
        <p id="statusText" class="text-sm font-semibold text-slate-800">Preparing installation...</p>
    </div>

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
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch(e) {
                        throw new Error('Server returned HTML instead of JSON (HTTP ' + res.status + '). Check if migration completed successfully.');
                    }
                });
            }
            return res.json();
        })
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
    <div class="bg-slate-950 rounded-md p-4 max-h-60 overflow-y-auto mb-4 ring-1 ring-slate-800">
        <pre id="outputLog" class="text-[11px] leading-relaxed text-slate-300/90 font-mono whitespace-pre-wrap">$ waiting for installation to start...</pre>
    </div>

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
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch(e) {
                        throw new Error('Server returned HTML instead of JSON (HTTP ' + res.status + '). Check if migration completed successfully.');
                    }
                });
            }
            return res.json();
        })
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
    <div class="w-full bg-gray-100 rounded-full h-1.5 mb-6 overflow-hidden">
        <div id="progressBar" class="bg-slate-800 h-1.5 rounded-full transition-all duration-500" style="width: 0%"></div>
    </div>

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
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch(e) {
                        throw new Error('Server returned HTML instead of JSON (HTTP ' + res.status + '). Check if migration completed successfully.');
                    }
                });
            }
            return res.json();
        })
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
    <div id="resultBox" class="hidden">
        <div id="successBox" class="hidden">
            <div class="flex items-center gap-3 bg-white border-l-2 border-emerald-500 rounded-md p-4 mb-4 ring-1 ring-gray-200">
                <i class="hgi-stroke hgi-checkmark-circle-02 text-3xl text-emerald-600 shrink-0"></i>
                <div>
                    <p class="text-sm font-bold text-gray-900">Installation Complete</p>
                    <p class="text-xs text-gray-500 mt-0.5">Database migrated and seeded successfully.</p>
                </div>
            </div>
            <a href="{{ route('install.complete') }}" class="w-full flex items-center justify-center gap-2 h-11 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded transition-all">
                Continue
                <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
            </a>
        </div>
        <div id="errorBox" class="hidden">
            <div class="flex items-center gap-3 bg-white border-l-2 border-red-500 rounded-md p-4 mb-4 ring-1 ring-gray-200">
                <i class="hgi-stroke hgi-alert-circle text-3xl text-red-500 shrink-0"></i>
                <div>
                    <p class="text-sm font-bold text-red-700">Installation Failed</p>
                    <p id="errorMessage" class="text-xs text-red-600 mt-0.5"></p>
                </div>
            </div>
            <button onclick="runInstall()" class="w-full flex items-center justify-center gap-2 h-11 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-bold rounded transition-all">
                <i class="hgi-stroke hgi-refresh text-base"></i>
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
                'Accept': 'application/json',
            },
        })
        .then(res => {
            if (!res.ok) {
                return res.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        return data;
                    } catch(e) {
                        throw new Error('Server returned HTML instead of JSON (HTTP ' + res.status + '). Check if migration completed successfully.');
                    }
                });
            }
            return res.json();
        })
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
