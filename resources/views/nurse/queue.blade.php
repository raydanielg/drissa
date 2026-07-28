@extends('layouts.dashboard')

@section('title', 'Vitals Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Vitals Queue')

@section('content')
<div class="space-y-6">

    @if(session('status'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 text-sm text-emerald-700">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Patients Waiting for Vitals</h3>
            <p class="text-xs text-gray-400">Record vitals then the receptionist will assign a doctor</p>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($visits as $visit)
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ strtoupper(substr($visit->patient->fullName(), 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->patient->mrn }} • Registered at {{ $visit->registered_at->format('H:i') }}</div>
                        </div>
                        @if($visit->vitals)
                            <span class="px-2 py-1 rounded-full text-[10px] font-medium bg-green-100 text-green-700">Vitals Taken</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-[10px] font-medium bg-amber-100 text-amber-700">Pending</span>
                        @endif
                    </div>

                    {{-- Vitals Form --}}
                    <form action="{{ route('nurse.visits.vitals', $visit) }}" method="POST" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Temp (°C)</label>
                            <input type="number" step="0.1" name="temperature" value="{{ $visit->vitals?->temperature }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Blood Pressure</label>
                            <input type="text" name="blood_pressure" placeholder="120/80" value="{{ $visit->vitals?->blood_pressure }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Pulse (bpm)</label>
                            <input type="number" name="pulse" value="{{ $visit->vitals?->pulse }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Weight (kg)</label>
                            <input type="number" step="0.1" name="weight" value="{{ $visit->vitals?->weight }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Height (cm)</label>
                            <input type="number" step="0.1" name="height" value="{{ $visit->vitals?->height }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Resp Rate</label>
                            <input type="number" name="respiratory_rate" value="{{ $visit->vitals?->respiratory_rate }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">O2 Sat (%)</label>
                            <input type="number" step="0.1" name="oxygen_saturation" value="{{ $visit->vitals?->oxygen_saturation }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Notes</label>
                            <input type="text" name="notes" value="{{ $visit->vitals?->notes }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                        </div>
                        <div class="col-span-2 md:col-span-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">
                                {{ $visit->vitals ? 'Update Vitals' : 'Save Vitals' }}
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400 text-sm">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    No patients waiting for vitals
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
