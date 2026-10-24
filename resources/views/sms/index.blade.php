@extends('layouts.dashboard')

@section('title', 'Send SMS - ' . config('app.name', 'Laravel'))
@section('page_title', 'Send SMS')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">Send SMS</h1>
        <p class="text-xs text-slate-400 mt-1">Send a text message to a user, patient, or manual number.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
        <form method="POST" action="{{ route('sms.store') }}" class="space-y-4" data-ajax>
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recipient Type</label>
                <x-ui.select name="recipient_type" id="recipientType" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="manual">Manual Phone</option>
                    <option value="user">System User</option>
                    <option value="patient">Patient</option>
                </x-ui.select>
            </div>

            <div id="userField" class="hidden">
                <label class="block text-xs font-medium text-gray-700 mb-1">Select User</label>
                <x-ui.select name="user_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="">Choose user...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->phone ?? 'No phone' }})</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div id="patientField" class="hidden">
                <label class="block text-xs font-medium text-gray-700 mb-1">Select Patient</label>
                <x-ui.select name="patient_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="">Choose patient...</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->phone ?? 'No phone' }})</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div id="phoneField">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
                <x-ui.input type="text" name="phone" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" placeholder="+255..." />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
                <x-ui.textarea name="message" id="smsMessage" rows="5" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required maxlength="1600"></x-ui.textarea>
                <div class="text-[10px] text-gray-400 mt-1 text-right"><span id="charCount">0</span>/1600</div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Templates</label>
                <x-ui.select id="templateSelect" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="">Select template...</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->body }}">{{ $template->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Send SMS</button>
        </form>
    </div>
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
