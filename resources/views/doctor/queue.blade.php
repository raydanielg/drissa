@extends('layouts.dashboard')

@section('title', 'Doctor Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Queue')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">My Patients</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visits as $visit)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                            <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $visit->status) }}</td>
                            <td class="px-6 py-3 space-y-2">
                                @if ($visit->status === \App\Enums\VisitStatus::WaitingForDoctor->value)
                                    <form method="POST" action="{{ route('doctor.visits.call', $visit) }}">
                                        @csrf
                                        <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg">Call In</button>
                                    </form>
                                @endif

                                @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value || $visit->status === \App\Enums\VisitStatus::LabCompleted->value)
                                    {{-- Consultation Form --}}
                                    <form method="POST" action="{{ route('doctor.visits.consult', $visit) }}" class="space-y-2 border rounded-lg p-3 bg-gray-50">
                                        @csrf
                                        <textarea name="history" placeholder="History" class="w-full border rounded-lg px-2 py-1 text-xs" rows="2">{{ $visit->consultation?->history }}</textarea>
                                        <textarea name="examination" placeholder="Examination" class="w-full border rounded-lg px-2 py-1 text-xs" rows="2">{{ $visit->consultation?->examination }}</textarea>
                                        <textarea name="diagnosis" placeholder="Diagnosis" class="w-full border rounded-lg px-2 py-1 text-xs" rows="2">{{ $visit->consultation?->diagnosis }}</textarea>
                                        <textarea name="notes" placeholder="Notes" class="w-full border rounded-lg px-2 py-1 text-xs" rows="2">{{ $visit->consultation?->notes }}</textarea>
                                        <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg">Save Consultation</button>
                                    </form>

                                    {{-- Lab Order --}}
                                    <form method="POST" action="{{ route('doctor.visits.lab', $visit) }}" class="border rounded-lg p-3 bg-gray-50">
                                        @csrf
                                        <select name="test_ids[]" multiple class="w-full border rounded-lg px-2 py-1 text-xs" size="4">
                                            @foreach ($labTests as $test)
                                                <option value="{{ $test->id }}">{{ $test->name }}</option>
                                            @endforeach
                                        </select>
                                        <textarea name="notes" placeholder="Clinical notes" class="w-full border rounded-lg px-2 py-1 text-xs mt-2" rows="2"></textarea>
                                        <button type="submit" class="bg-sky-500 text-white text-xs font-medium px-3 py-1 rounded-lg mt-2">Order Lab</button>
                                    </form>

                                    {{-- Prescription --}}
                                    <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="border rounded-lg p-3 bg-gray-50">
                                        @csrf
                                        <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                                            <div class="grid grid-cols-6 gap-2">
                                                <select name="items[0][medication_id]" class="col-span-2 border rounded-lg px-2 py-1 text-xs">
                                                    <option value="">Drug</option>
                                                    @foreach ($medications as $med)
                                                        <option value="{{ $med->id }}">{{ $med->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" name="items[0][quantity]" placeholder="Qty" class="border rounded-lg px-2 py-1 text-xs">
                                                <input type="text" name="items[0][dosage]" placeholder="Dose" class="border rounded-lg px-2 py-1 text-xs">
                                                <input type="text" name="items[0][frequency]" placeholder="Freq" class="border rounded-lg px-2 py-1 text-xs">
                                                <input type="text" name="items[0][duration]" placeholder="Duration" class="border rounded-lg px-2 py-1 text-xs">
                                            </div>
                                        </div>
                                        <button type="submit" class="bg-violet-500 text-white text-xs font-medium px-3 py-1 rounded-lg mt-2">Prescribe</button>
                                    </form>

                                    {{-- Send to Payment --}}
                                    <form method="POST" action="{{ route('doctor.visits.payment', $visit) }}">
                                        @csrf
                                        <button type="submit" class="bg-gold-400 text-gray-900 text-xs font-medium px-3 py-1 rounded-lg">Send to Payment</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No patients in queue</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
