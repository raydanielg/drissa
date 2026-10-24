@extends('layouts.dashboard')

@section('title', 'Vitals Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Vitals Queue')

@section('content')
<div class="space-y-5">

    @if(session('status'))
    <div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 text-xs text-emerald-700">
        {{ session('status') }}
    </div>
    @endif

    {{-- Vitals Form --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Nurse</p>
            <h1 class="text-xl font-bold">Vitals Queue</h1>
            <p class="text-xs text-slate-400 mt-1">Record vitals for registered patients.</p>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Patients Waiting for Vitals</h3>
            <p class="text-xs text-gray-400 mt-0.5">Record vitals then the receptionist will assign a doctor</p>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($visits as $visit)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 text-sm font-bold">
                            {{ strtoupper(substr($visit->patient->fullName(), 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->patient->mrn }} • Registered at {{ $visit->registered_at->format('H:i') }}</div>
                        </div>
                        @if($visit->vitals)
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Vitals Taken</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>
                        @endif
                    </div>

                    {{-- Vitals Form --}}
                    <form action="{{ route('nurse.visits.vitals', $visit) }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Temp (°C)</label>
                            <x-ui.input type="number" step="0.1" name="temperature" value="{{ $visit->vitals?->temperature }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Blood Pressure</label>
                            <x-ui.input type="text" name="blood_pressure" placeholder="120/80" value="{{ $visit->vitals?->blood_pressure }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Pulse (bpm)</label>
                            <x-ui.input type="number" name="pulse" value="{{ $visit->vitals?->pulse }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Weight (kg)</label>
                            <x-ui.input type="number" step="0.1" name="weight" value="{{ $visit->vitals?->weight }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Height (cm)</label>
                            <x-ui.input type="number" step="0.1" name="height" value="{{ $visit->vitals?->height }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Resp Rate</label>
                            <x-ui.input type="number" name="respiratory_rate" value="{{ $visit->vitals?->respiratory_rate }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">O2 Sat (%)</label>
                            <x-ui.input type="number" step="0.1" name="oxygen_saturation" value="{{ $visit->vitals?->oxygen_saturation }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Notes</label>
                            <x-ui.input type="text" name="notes" value="{{ $visit->vitals?->notes }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                        </div>
                        <div class="col-span-2 md:col-span-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">
                                {{ $visit->vitals ? 'Update Vitals' : 'Save Vitals' }}
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400 text-sm">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-6xl mx-auto mb-2 text-gray-300"></i>
                    No patients waiting for vitals
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
