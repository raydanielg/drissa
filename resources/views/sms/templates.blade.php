@extends('layouts.dashboard')

@section('title', 'SMS Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Templates')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Filters Bar --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter:</span>
            </div>
            <select id="filterType" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none md:w-48" onchange="loadTemplates()">
                <option value="">All Types</option>
                @php
                    $allTypes = ['registration'=>'Registration','appointment'=>'Appointment','visit'=>'Visit','doctor'=>'Doctor','lab'=>'Lab','pharmacy'=>'Pharmacy','payment'=>'Payment','birthday'=>'Birthday','holiday'=>'Holiday','general'=>'General','reminder'=>'Reminder','marketing'=>'Marketing'];
                @endphp
                @foreach ($allTypes as $t => $label)
                    <option value="{{ $t }}">{{ $label }}</option>
                @endforeach
            </select>
            <select id="filterStatus" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none md:w-40" onchange="loadTemplates()">
                <option value="">All Status</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </select>
            <div class="flex-1 relative">
                <input type="text" id="searchInput" placeholder="Search templates..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" onkeyup="debouncedSearch()">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-all flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Template</span>
            </button>
        </div>
    </div>

    {{-- AJAX Content Container --}}
    <div id="ajaxContent">
        @include('sms.partials.templates-table')
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
                        @foreach ($allTypes as $t => $label)
                            <option value="{{ $t }}">{{ $label }}</option>
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
                        @foreach ($allTypes as $t => $label)
                            <option value="{{ $t }}">{{ $label }}</option>
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
    let currentPage = {{ $templates->currentPage() }};
    let perPage = {{ (int) request('per_page', 20) }};
    let searchTimer = null;

    function loadTemplates(page) {
        if (page === undefined) page = currentPage;
        const type = document.getElementById('filterType').value;
        const status = document.getElementById('filterStatus').value;
        const search = document.getElementById('searchInput').value;

        const params = new URLSearchParams();
        if (type) params.set('type', type);
        if (status) params.set('status', status);
        if (search) params.set('search', search);
        if (perPage) params.set('per_page', perPage);
        params.set('page', page);

        const container = document.getElementById('ajaxContent');
        container.style.opacity = '0.5';

        fetch('{{ route("sms.templates") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            container.innerHTML = data.html;
            container.style.opacity = '1';
            currentPage = page;
            attachPaginationListeners();
        })
        .catch(() => {
            container.style.opacity = '1';
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Failed to load templates', showConfirmButton: false, timer: 3000 });
        });
    }

    function changePerPage(value) {
        perPage = parseInt(value);
        currentPage = 1;
        loadTemplates();
    }

    function debouncedSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadTemplates();
        }, 400);
    }

    function attachPaginationListeners() {
        document.querySelectorAll('#ajaxContent .pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page') || 1;
                loadTemplates(page);
            });
        });
    }

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
            confirmButtonText: 'Yes, Delete',
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

    // Override default AJAX form success to reload table instead of full page
    document.addEventListener('DOMContentLoaded', function() {
        attachPaginationListeners();

        // Intercept data-ajax form submissions to reload table on success
        document.querySelectorAll('form[data-ajax]').forEach(form => {
            form.addEventListener('submit', async function(e) {
                if (e.defaultPrevented && e.target !== this) return;
            });
        });

        // Close modals on backdrop click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
        document.getElementById('editTemplateModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });
</script>
@endpush
@endsection
