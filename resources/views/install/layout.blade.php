<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Installer') - Uzazi Clinic</title>
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Nunito', sans-serif; }
        .step-active { background: linear-gradient(135deg, #024938, #f9ac00); }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ asset('logo.png') }}" alt="Uzazi Clinic" class="w-16 h-16 mx-auto rounded-2xl object-cover shadow-lg mb-3">
            <h1 class="text-2xl font-extrabold text-white">Uzazi Clinic</h1>
            <p class="text-emerald-200/60 text-sm">Installation Wizard</p>
        </div>

        {{-- Steps --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            @php
                $currentStep = $currentStep ?? 1;
                $steps = [1 => 'Requirements', 2 => 'Database', 3 => 'Install', 4 => 'Complete'];
            @endphp
            @foreach($steps as $num => $label)
                <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $num < $currentStep ? 'bg-gold-500 text-white' : ($num === $currentStep ? 'step-active text-white' : 'bg-white/10 text-white/40') }}">
                            @if($num < $currentStep)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="text-xs font-semibold {{ $num === $currentStep ? 'text-white' : 'text-white/40' }} hidden sm:inline">{{ $label }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 mx-2 {{ $num < $currentStep ? 'bg-gold-500' : 'bg-white/10' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            @yield('content')
        </div>

        <p class="text-center text-xs text-emerald-200/40 mt-6">&copy; {{ date('Y') }} Uzazi Clinic</p>
    </div>

    @stack('scripts')
</body>
</html>
