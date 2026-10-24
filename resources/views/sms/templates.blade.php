@extends('layouts.dashboard')

@section('title', 'SMS Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Templates')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

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
            confirmButtonColor: '#0f172a',
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
                <h1 class="text-xl font-bold">SMS Templates</h1>
                <p class="text-xs text-slate-400 mt-1">Manage reusable message templates.</p>
            </div>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-add-01 text-sm"></i>
                <span>New Template</span>
            </button>
        </div>
    </div>

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
            confirmButtonColor: '#0f172a',
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
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                <i class="hgi-stroke hgi-filter-horizontal text-base text-gray-400"></i>
                <span>Filter:</span>
            </div>
            <x-ui.select id="filterType" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none md:w-48" onchange="loadTemplates()">
                <option value="">All Types</option>
                @php
                    $allTypes = ['registration'=>'Registration','appointment'=>'Appointment','visit'=>'Visit','doctor'=>'Doctor','lab'=>'Lab','pharmacy'=>'Pharmacy','payment'=>'Payment','birthday'=>'Birthday','holiday'=>'Holiday','general'=>'General','reminder'=>'Reminder','marketing'=>'Marketing'];
                @endphp
                @foreach ($allTypes as $t => $label)
                    <option value="{{ $t }}">{{ $label }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select id="filterStatus" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none md:w-40" onchange="loadTemplates()">
                <option value="">All Status</option>
                <option value="active">Active Only</option>
                <option value="inactive">Inactive Only</option>
            </x-ui.select>
            <div class="flex-1 relative">
                <x-ui.input type="text" id="searchInput" placeholder="Search templates..." class="w-full border border-gray-200 rounded pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" onkeyup="debouncedSearch()" />
                <i class="hgi-stroke hgi-search-01 text-base text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            </div>
        </div>
    </div>

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
            confirmButtonColor: '#0f172a',
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
    <div id="ajaxContent">
        @include('sms.partials.templates-table')
    </div>
</div>

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
            confirmButtonColor: '#0f172a',
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
<div id="addModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-md w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">New SMS Template</h3>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('sms.templates.store') }}" class="p-5 space-y-4" data-ajax>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name</label>
                    <x-ui.input type="text" name="name" placeholder="e.g. Appointment Reminder" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
                    <x-ui.select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required>
                        @foreach ($allTypes as $t => $label)
                            <option value="{{ $t }}">{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject <span class="text-gray-400 font-normal">(optional)</span></label>
                <x-ui.input type="text" name="subject" placeholder="e.g. Kumbusho la Miadi" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Body</label>
                <x-ui.textarea name="body" placeholder="Use &#123;&#123;name&#125;&#125;, &#123;&#123;date&#125;&#125;, &#123;&#123;time&#125;&#125;, &#123;&#123;amount&#125;&#125;, &#123;&#123;phone&#125;&#125; etc." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="4" required></x-ui.textarea>
                <p class="text-[10px] text-gray-400 mt-1">Available placeholders: &#123;&#123;name&#125;&#125;, &#123;&#123;date&#125;&#125;, &#123;&#123;time&#125;&#125;, &#123;&#123;amount&#125;&#125;, &#123;&#123;phone&#125;&#125;, &#123;&#123;mrn&#125;&#125;, &#123;&#123;doctor&#125;&#125;, &#123;&#123;medication&#125;&#125;, &#123;&#123;discount&#125;&#125;, &#123;&#123;clinic&#125;&#125;</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors flex items-center gap-2">
                    <i class="hgi-stroke hgi-tick-02 text-base"></i>
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>

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
            confirmButtonColor: '#0f172a',
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
<div id="editTemplateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-md w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Edit SMS Template</h3>
            <button type="button" onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
            </button>
        </div>
        <form id="editTemplateForm" method="POST" class="p-5 space-y-4" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name</label>
                    <x-ui.input type="text" name="name" id="editName" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
                    <x-ui.select name="type" id="editType" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required>
                        @foreach ($allTypes as $t => $label)
                            <option value="{{ $t }}">{{ $label }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Subject <span class="text-gray-400 font-normal">(optional)</span></label>
                <x-ui.input type="text" name="subject" id="editSubject" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Message Body</label>
                <x-ui.textarea name="body" id="editBody" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="4" required></x-ui.textarea>
            </div>
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                <input type="checkbox" name="is_active" id="editActive" value="1" class="w-4 h-4 rounded border-gray-300 text-slate-600 focus:ring-slate-300">
                <label for="editActive" class="text-sm text-gray-700 font-medium">Active</label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="px-4 py-2 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors flex items-center gap-2">
                    <i class="hgi-stroke hgi-refresh text-base"></i>
                    Update Template
                </button>
            </div>
        </form>
    </div>
</div>

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
            confirmButtonColor: '#0f172a',
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
            confirmButtonColor: '#0f172a',
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
