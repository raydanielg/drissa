@extends('layouts.dashboard')

@section('title', 'Lab Test Types - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Test Types')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Lab Test Types</h2>
            <p class="text-sm text-gray-500">Manage laboratory tests, prices and reference ranges</p>
        </div>
        <button type="button" onclick="openAddLabTestPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Test Type
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $tests->total() }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Total Types</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $tests->where('is_active', true)->count() }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Active</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $tests->where('is_active', false)->count() }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Inactive</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">TSh {{ number_format($tests->sum('price'), 0) }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Total Price</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Unit</th>
                        <th class="px-6 py-3">Reference Range</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tests as $test)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-gray-900">{{ $test->name }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $test->code ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $test->unit ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $test->reference_range ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700 font-medium">TSh {{ number_format($test->price, 2) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $test->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $test->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $test->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditLabTestPanel('{{ route('lab-tests.edit', $test) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('lab-tests.destroy', $test) }}" data-ajax data-confirm="Delete this test type?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                <p>No test types found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tests->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="labTestSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="labTestBackdrop" onclick="closeLabTestSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="labTestPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="labTestSlideTitle">Edit Test Type</h3>
                    <p class="text-xs text-gray-500" id="labTestSlideSubtitle">Update test type details</p>
                </div>
                <button onclick="closeLabTestSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="labTestSlideContent"></div>
        </div>
    </div>
</div>

<template id="labTestFormTemplate">
    <form id="labTestForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="lt_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="lt_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" id="lt_code" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                <input type="text" name="unit" id="lt_unit" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range</label>
                <input type="text" name="reference_range" id="lt_reference_range" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Price <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="price" id="lt_price" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" id="lt_is_active" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="lt_is_active" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="lt_description" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="4"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeLabTestSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="lt_submit_btn">Save Test Type</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
        form.action = action;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 2000, showConfirmButton: false });
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }
</script>
@endpush
@endsection
