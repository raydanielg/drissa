@extends('layouts.dashboard')

@section('title', 'Manager Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Manager Reports')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Manager Reports</h2>
            <p class="text-sm text-gray-500">{{ auth()->user()->isAdmin() ? 'All daily & weekly reports from clinic managers' : 'Your daily & weekly clinic reports' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-emerald-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Daily
            </a>
            <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Weekly
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('manager.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">Type</label>
                <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All</option>
                    <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">Filter</button>
            <a href="{{ route('manager.reports.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Period</th>
                        @if(auth()->user()->isAdmin())<th class="px-6 py-3">Manager</th>@endif
                        <th class="px-6 py-3 text-right">Revenue</th>
                        <th class="px-6 py-3 text-right">New</th>
                        <th class="px-6 py-3 text-right">Returning</th>
                        <th class="px-6 py-3 text-right">Expenses</th>
                        <th class="px-6 py-3">Submitted</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                    <tr class="hover:bg-emerald-50/40 transition-colors">
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $report->type === 'weekly' ? 'bg-gold-100 text-gold-700' : 'bg-emerald-100 text-emerald-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $report->type === 'weekly' ? 'bg-gold-500' : 'bg-emerald-500' }}"></span>
                                {{ ucfirst($report->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-xs font-medium text-gray-700">{{ $report->periodLabel() }}</td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-[10px] font-bold">{{ strtoupper(substr($report->user->name, 0, 1)) }}</div>
                                <span class="text-xs text-gray-700">{{ $report->user->name }}</span>
                            </div>
                        </td>
                        @endif
                        <td class="px-6 py-3.5 text-right text-xs font-semibold text-emerald-700">{{ $report->revenue_overall !== null ? number_format($report->revenue_overall) . ' TSh' : '—' }}</td>
                        <td class="px-6 py-3.5 text-right text-xs text-gray-600">{{ $report->new_clients ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-right text-xs text-gray-600">{{ $report->returning_clients ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-right text-xs text-rose-600">{{ number_format($report->totalExpenses()) }} TSh</td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('manager.reports.show', $report) }}" class="p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.reports.edit', $report) }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('manager.reports.destroy', $report) }}" data-ajax data-confirm="Delete this {{ $report->type }} report?" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm">No reports found</p>
                                <a href="{{ route('manager.reports.create') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Create your first report</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
