@php
    $isWeekly = $type === 'weekly';
    $periodLabel = $isWeekly ? "This Week's Report" : "Today's Report";
    $r = $report ?? null;
    $val = fn(string $key) => old($key, $r?->{$key} ?? ($suggestions[$key] ?? null));
    $deptRows = old('department_reports', $r?->department_reports ?? []);
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Report header --}}
    <div class="bg-emerald-900 px-6 py-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-gold-400 font-bold">{{ config('app.name', 'Laravel') }} · Report From Every Department</p>
                <h2 class="text-xl font-extrabold text-white mt-1">{{ $isWeekly ? 'WEEKLY MANAGER REPORT' : 'DAILY MANAGER REPORT' }}</h2>
            </div>
            <div class="text-right">
                <p class="text-xs text-emerald-200/70">{{ $isWeekly ? 'Week' : 'Date' }}</p>
                <p class="text-sm font-semibold text-white">{{ $start->format('d M Y') }}@if($isWeekly) — {{ $end->format('d M Y') }}@endif</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">
        {{-- Period picker --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @if($isWeekly)
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Week Start <span class="text-red-500">*</span></label>
                    <input type="date" name="week_start" id="week_start" value="{{ old('week_start', $r?->week_start?->format('Y-m-d') ?? $start->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Week End <span class="text-red-500">*</span></label>
                    <input type="date" name="week_end" id="week_end" value="{{ old('week_end', $r?->week_end?->format('Y-m-d') ?? $end->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <input type="hidden" name="report_date" id="report_date" value="{{ old('report_date', $r?->report_date?->format('Y-m-d') ?? $end->format('Y-m-d')) }}">
            @else
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Report Date <span class="text-red-500">*</span></label>
                    <input type="date" name="report_date" id="report_date" value="{{ old('report_date', $r?->report_date?->format('Y-m-d') ?? $reportDate->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
            @endif
            <input type="hidden" name="type" value="{{ $type }}">
            @if(! $r)
            <div class="flex items-end">
                <button type="button" id="btnSuggestions" class="inline-flex items-center gap-2 bg-gold-500 hover:bg-gold-600 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Fill From System
                </button>
            </div>
            @endif
        </div>

        @if(isset($existing) && $existing)
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-xs text-amber-800">A {{ $type }} report for this period already exists. <a href="{{ route('manager.reports.edit', $existing) }}" class="font-semibold underline">Edit it here</a> or pick another period.</p>
        </div>
        @endif

        {{-- Items table (matches the paper form) --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500 w-1/2">Item</th>
                        <th class="px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $periodLabel }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Revenue Overall <span class="text-[10px] text-gray-400">(TSh)</span></td>
                        <td class="px-3 py-1.5"><input type="number" step="0.01" min="0" name="revenue_overall" data-suggest="revenue_overall" value="{{ $val('revenue_overall') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Total Supplement Sales PPV <span class="text-[10px] text-gray-400">(TSh)</span></td>
                        <td class="px-3 py-1.5"><input type="number" step="0.01" min="0" name="supplement_sales_ppv" value="{{ $val('supplement_sales_ppv') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Total Products Sales [Costs] <span class="text-[10px] text-gray-400">(TSh)</span></td>
                        <td class="px-3 py-1.5"><input type="number" step="0.01" min="0" name="product_sales_costs" data-suggest="product_sales_costs" value="{{ $val('product_sales_costs') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Total New Clients [Check up]</td>
                        <td class="px-3 py-1.5"><input type="number" step="1" min="0" name="new_clients" data-suggest="new_clients" value="{{ $val('new_clients') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Returning Clients [Follow up]</td>
                        <td class="px-3 py-1.5"><input type="number" step="1" min="0" name="returning_clients" data-suggest="returning_clients" value="{{ $val('returning_clients') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Laboratory Tools Expenses <span class="text-[10px] text-gray-400">(TSh)</span></td>
                        <td class="px-3 py-1.5"><input type="number" step="0.01" min="0" name="lab_tools_expenses" value="{{ $val('lab_tools_expenses') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00"></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm font-medium text-gray-700">Office Expenses [Matumizi] <span class="text-[10px] text-gray-400">(TSh)</span></td>
                        <td class="px-3 py-1.5"><input type="number" step="0.01" min="0" name="office_expenses" value="{{ $val('office_expenses') }}" class="w-full border border-gray-200 rounded-md px-3 py-1.5 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="0.00"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Report from every department --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Report From Every Department</label>
                <button type="button" id="addDeptRow" class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Department
                </button>
            </div>
            <div id="deptRows" class="space-y-2">
                @foreach($deptRows as $i => $row)
                <div class="dept-row grid grid-cols-1 sm:grid-cols-[220px_1fr_auto] gap-2 items-start">
                    <select name="department_reports[{{ $i }}][name]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select department...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ ($row['name'] ?? '') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="department_reports[{{ $i }}][report]" value="{{ $row['report'] ?? '' }}" placeholder="Department report summary..." class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <button type="button" class="remove-dept p-2 text-red-500 hover:bg-red-50 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                @endforeach
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Ongeza ripoti fupi kwa kila department (Reception, Doctor, Lab, Pharmacy, n.k.)</p>
        </div>

        {{-- Narrative sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Staff Performance For Every Department</label>
                <textarea name="staff_performance" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Performance ya staff kwa kila department...">{{ $val('staff_performance') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Challenges Happened</label>
                <textarea name="challenges" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Changamoto zilizotokea...">{{ $val('challenges') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Action Plan Taken</label>
                <textarea name="action_plan" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Hatua zilizochukuliwa...">{{ $val('action_plan') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">{{ $isWeekly ? 'Weekly Meeting Reports And Comments' : 'Meeting Reports And Comments' }}</label>
                <textarea name="meeting_reports" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ripoti za mikutano na maoni...">{{ $val('meeting_reports') }}</textarea>
            </div>
        </div>

        {{-- Signature --}}
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 pt-4 border-t border-gray-100">
            <div class="flex-1 max-w-sm">
                <label class="block text-xs font-medium text-gray-700 mb-1">Manager Signature</label>
                <input type="text" name="manager_signature" value="{{ $val('manager_signature') ?? auth()->user()->name }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-semibold italic focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Jina la manager">
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.reports.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $r ? 'Update Report' : 'Submit Report' }}
                </button>
            </div>
        </div>
    </div>
</div>

<template id="deptRowTemplate">
    <div class="dept-row grid grid-cols-1 sm:grid-cols-[220px_1fr_auto] gap-2 items-start">
        <select name="department_reports[__i__][name]" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">Select department...</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
            @endforeach
        </select>
        <input type="text" name="department_reports[__i__][report]" placeholder="Department report summary..." class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        <button type="button" class="remove-dept p-2 text-red-500 hover:bg-red-50 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
</template>

@push('scripts')
<script>
(function() {
    // Department rows
    let deptIndex = {{ count($deptRows) }};
    const deptRows = document.getElementById('deptRows');
    const tpl = document.getElementById('deptRowTemplate');

    document.getElementById('addDeptRow')?.addEventListener('click', function() {
        const html = tpl.innerHTML.replaceAll('__i__', deptIndex++);
        const div = document.createElement('div');
        div.innerHTML = html.trim();
        deptRows.appendChild(div.firstElementChild);
    });

    deptRows?.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-dept');
        if (btn) btn.closest('.dept-row').remove();
    });

    @if(! $r)
    // Auto-fill from system suggestions
    const type = @json($type);
    async function fillFromSystem() {
        const params = new URLSearchParams({ type });
        @if($isWeekly)
            params.set('date', document.getElementById('week_start').value);
        @else
            params.set('date', document.getElementById('report_date').value);
        @endif
        try {
            const res = await fetch('{{ route("manager.reports.suggestions") }}?' + params, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const data = await res.json();
            document.querySelectorAll('[data-suggest]').forEach(input => {
                if (data[input.dataset.suggest] !== undefined) input.value = data[input.dataset.suggest];
            });
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'System figures loaded', timer: 2000, showConfirmButton: false });
        } catch (e) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Could not load system figures', timer: 3000, showConfirmButton: false });
        }
    }
    document.getElementById('btnSuggestions')?.addEventListener('click', fillFromSystem);
    @endif
})();
</script>
@endpush
