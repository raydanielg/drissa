@extends('layouts.dashboard')

@section('title', 'SMS Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Templates')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $allTypes = ['registration','appointment','visit','doctor','lab','pharmacy','payment','birthday','holiday','general','reminder','marketing'];
            $typeLabels = [
                'registration' => 'Registration', 'appointment' => 'Appointment', 'visit' => 'Visit',
                'doctor' => 'Doctor', 'lab' => 'Lab', 'pharmacy' => 'Pharmacy',
                'payment' => 'Payment', 'birthday' => 'Birthday', 'holiday' => 'Holiday',
                'general' => 'General', 'reminder' => 'Reminder', 'marketing' => 'Marketing',
            ];
        @endphp
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $templates->total() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active</span>
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ $templates->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Categories</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600">{{ count($allTypes) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Inactive</span>
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-500">{{ $templates->where('is_active', false)->count() }}</p>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter:</span>
            </div>
            <select id="filterType" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none md:w-48">
                <option value="">All Types</option>
                @foreach ($allTypes as $t)
                    <option value="{{ $t }}">{{ $typeLabels[$t] }}</option>
                @endforeach
            </select>
            <select id="filterStatus" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none md:w-40">
                <option value="">All Status</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </select>
            <div class="flex-1"></div>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Template</span>
            </button>
        </div>
    </div>

    {{-- Templates Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="templatesTable">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">#</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">Subject</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">Message</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition-colors template-row" data-type="{{ $template->type }}" data-active="{{ $template->is_active ? 'active' : 'inactive' }}">
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $template->id }}</td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-900">{{ $template->name }}</div>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $typeColors = [
                                        'registration' => 'bg-purple-100 text-purple-700',
                                        'appointment' => 'bg-blue-100 text-blue-700',
                                        'visit' => 'bg-indigo-100 text-indigo-700',
                                        'doctor' => 'bg-cyan-100 text-cyan-700',
                                        'lab' => 'bg-teal-100 text-teal-700',
                                        'pharmacy' => 'bg-emerald-100 text-emerald-700',
                                        'payment' => 'bg-amber-100 text-amber-700',
                                        'birthday' => 'bg-pink-100 text-pink-700',
                                        'holiday' => 'bg-red-100 text-red-700',
                                        'general' => 'bg-gray-100 text-gray-700',
                                        'reminder' => 'bg-orange-100 text-orange-700',
                                        'marketing' => 'bg-fuchsia-100 text-fuchsia-700',
                                    ];
                                    $color = $typeColors[$template->type] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $color }}">{{ $typeLabels[$template->type] ?? ucfirst($template->type) }}</span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $template->subject ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs max-w-xs">
                                <span class="line-clamp-2">{{ Str::limit($template->body, 80) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if($template->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="editTemplate({{ $template->id }}, {{ json_encode($template->name) }}, {{ json_encode($template->body) }}, {{ json_encode($template->type) }}, {{ json_encode($template->subject ?? '') }}, {{ $template->is_active ? 1 : 0 }})" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" onclick="deleteTemplate({{ $template->id }}, {{ json_encode($template->name) }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            No templates found
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $templates->links() }}</div>
        @endif
    </div>
</div>

{{-- Add Template Modal --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">New SMS Template</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('sms.templates.store') }}" class="p-6 space-y-4" data-ajax>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name</label>
                    <input type="text" name="name" placeholder="e.g. Appointment Reminder" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
                    <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t }}">{{ $typeLabels[$t] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="subject" placeholder="e.g. Kumbusho la Miadi" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Body</label>
                <textarea name="body" placeholder="Use &#123;&#123;name&#125;&#125;, &#123;&#123;date&#125;&#125;, &#123;&#123;time&#125;&#125;, &#123;&#123;amount&#125;&#125;, &#123;&#123;phone&#125;&#125; etc." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" rows="4" required></textarea>
                <p class="text-[10px] text-gray-400 mt-1">Available placeholders: &#123;&#123;name&#125;&#125;, &#123;&#123;date&#125;&#125;, &#123;&#123;time&#125;&#125;, &#123;&#123;amount&#125;&#125;, &#123;&#123;phone&#125;&#125;, &#123;&#123;mrn&#125;&#125;, &#123;&#123;doctor&#125;&#125;, &#123;&#123;medication&#125;&#125;, &#123;&#123;discount&#125;&#125;, &#123;&#123;clinic&#125;&#125;</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Template Modal --}}
<div id="editTemplateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Edit SMS Template</h3>
            <button type="button" onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editTemplateForm" method="POST" class="p-6 space-y-4" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name</label>
                    <input type="text" name="name" id="editName" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
                    <select name="type" id="editType" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" required>
                        @foreach ($allTypes as $t)
                            <option value="{{ $t }}">{{ $typeLabels[$t] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" name="subject" id="editSubject" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Body</label>
                <textarea name="body" id="editBody" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" rows="4" required></textarea>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <input type="checkbox" name="is_active" id="editActive" value="1" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="editActive" class="text-sm text-gray-700 font-medium">Active</label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-all">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Update Template
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Form --}}
<form id="deleteForm" method="POST" class="hidden" data-ajax>
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    const allTypes = @json($allTypes);
    const typeLabels = @json($typeLabels);

    function editTemplate(id, name, body, type, subject, isActive) {
        const form = document.getElementById('editTemplateForm');
        form.action = '/sms/templates/' + id;
        document.getElementById('editName').value = name;
        document.getElementById('editBody').value = body;
        document.getElementById('editType').value = type;
        document.getElementById('editSubject').value = subject;
        document.getElementById('editActive').checked = isActive === 1;
        document.getElementById('editTemplateModal').classList.remove('hidden');
    }

    function deleteTemplate(id, name) {
        Swal.fire({
            title: 'Delete Template?',
            text: 'Are you sure you want to delete "' + name + '"? This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/sms/templates/' + id;
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        });
    }

    // Filtering
    function applyFilters() {
        const typeFilter = document.getElementById('filterType').value;
        const statusFilter = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('.template-row');

        rows.forEach(row => {
            const type = row.dataset.type;
            const status = row.dataset.active;
            const typeMatch = !typeFilter || type === typeFilter;
            const statusMatch = !statusFilter || status === statusFilter;
            row.style.display = (typeMatch && statusMatch) ? '' : 'none';
        });

        // Show empty message if no rows visible
        const visibleRows = document.querySelectorAll('.template-row');
        let anyVisible = false;
        visibleRows.forEach(r => { if (r.style.display !== 'none') anyVisible = true; });
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.style.display = anyVisible ? 'none' : '';
    }

    document.getElementById('filterType').addEventListener('change', applyFilters);
    document.getElementById('filterStatus').addEventListener('change', applyFilters);

    // Close modals on backdrop click
    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
    document.getElementById('editTemplateModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endpush
@endsection
