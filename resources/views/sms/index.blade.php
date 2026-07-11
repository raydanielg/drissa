@extends('layouts.dashboard')

@section('title', 'Send SMS - ' . config('app.name', 'Laravel'))
@section('page_title', 'Send SMS')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('sms.store') }}" class="space-y-4" data-ajax>
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Recipient Type</label>
            <select name="recipient_type" id="recipientType" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="manual">Manual Phone</option>
                <option value="user">System User</option>
                <option value="patient">Patient</option>
            </select>
        </div>

        <div id="userField" class="hidden">
            <label class="block text-xs font-medium text-gray-700 mb-1">Select User</label>
            <select name="user_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Choose user...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->phone ?? 'No phone' }})</option>
                @endforeach
            </select>
        </div>

        <div id="patientField" class="hidden">
            <label class="block text-xs font-medium text-gray-700 mb-1">Select Patient</label>
            <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Choose patient...</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->phone ?? 'No phone' }})</option>
                @endforeach
            </select>
        </div>

        <div id="phoneField">
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="+255...">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
            <textarea name="message" id="smsMessage" rows="5" class="w-full border rounded-lg px-3 py-2 text-sm" required maxlength="1600"></textarea>
            <div class="text-xs text-gray-500 mt-1 text-right"><span id="charCount">0</span>/1600</div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Templates</label>
            <select id="templateSelect" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select template...</option>
                @foreach($templates as $template)
                    <option value="{{ $template->body }}">{{ $template->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Send SMS</button>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('recipientType').addEventListener('change', function() {
        document.getElementById('userField').classList.toggle('hidden', this.value !== 'user');
        document.getElementById('patientField').classList.toggle('hidden', this.value !== 'patient');
        document.getElementById('phoneField').classList.toggle('hidden', this.value !== 'manual');
    });
    document.getElementById('templateSelect').addEventListener('change', function() {
        document.getElementById('smsMessage').value = this.value;
        document.getElementById('charCount').textContent = this.value.length;
    });
    document.getElementById('smsMessage').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });
</script>
@endpush
@endsection
