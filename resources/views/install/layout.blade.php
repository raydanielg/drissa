<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Installer') — Uzazi Clinic</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800&display=swap" rel="stylesheet">
    {{-- Card --}}
    {{-- Card --}}
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .45s cubic-bezier(.16,1,.3,1) both; }
        .step-dot { transition: all .3s ease; }
        .step-line { transition: background .3s ease; }
    </style>
</head>
<body class="min-h-screen antialiased text-slate-800 bg-gray-50">

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl fade-up">

            {{-- Card --}}
            <div class="flex items-center justify-center gap-3 mb-8">
                <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-11 h-11 rounded-md object-cover shadow-sm ring-1 ring-slate-200">
                <div class="text-left">
                    <h1 class="text-lg font-extrabold tracking-tight text-slate-900 leading-none">Uzazi Clinic</h1>
                    <p class="text-[11px] font-medium text-slate-500 tracking-widest uppercase mt-1">Setup Wizard</p>
                </div>
            </div>

            {{-- Card --}}
            @php
                $currentStep = $currentStep ?? 1;
                $steps = [1 => 'Requirements', 2 => 'Database', 3 => 'Install', 4 => 'Done'];
            @endphp
            <div class="flex items-center mb-8 px-2">
                @foreach($steps as $num => $label)
                    <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center gap-1.5">
                            <div class="step-dot w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold
                                {{ $num < $currentStep ? 'bg-slate-600 text-white' : ($num === $currentStep ? 'bg-slate-800 text-white ring-4 ring-slate-300/40' : 'bg-white text-slate-400 ring-1 ring-slate-200') }}">
                                @if($num < $currentStep)
                                    <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                                @else
                                    {{ $num }}
                                @endif
                            </div>
                            <span class="text-[10px] font-semibold {{ $num <= $currentStep ? 'text-slate-700' : 'text-slate-400' }}">{{ $label }}</span>
                        </div>
                        @if(!$loop->last)
                            <div class="step-line flex-1 h-px mx-3 mb-4 {{ $num < $currentStep ? 'bg-slate-500' : 'bg-slate-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-md ring-1 ring-gray-200 shadow-sm overflow-hidden">
                @yield('content')
            </div>

            <p class="text-center text-[11px] text-slate-400 mt-6">&copy; {{ date('Y') }} Uzazi Clinic &middot; Hospital &amp; Clinic Management</p>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
