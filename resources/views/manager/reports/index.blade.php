@extends('layouts.dashboard')

@section('title', 'Manager Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Manager Reports')

@section('content')
<div class="space-y-5">

    {{-- Table --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Manager</p>
                <h1 class="text-xl font-bold">Manager Reports</h1>
                <p class="text-xs text-slate-400 mt-1">{{ auth()->user()->isAdmin() ? 'All daily & weekly reports from clinic managers' : 'Your daily & weekly clinic reports' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    Daily
                </a>
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    Weekly
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <form method="GET" action="{{ route('manager.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">Type</label>
                <x-ui.select name="type" class="border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="">All</option>
                    <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                </x-ui.select>
            </div>
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">From</label>
                <x-ui.input type="text" data-fp-date name="from" value="{{ request('from') }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div>
                <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wider">To</label>
                <x-ui.input type="text" data-fp-date name="to" value="{{ request('to') }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">Filter</button>
            <a href="{{ route('manager.reports.index') }}" class="px-4 py-2 text-sm text-gray-500 border border-gray-200 hover:bg-gray-100 rounded transition-colors">Reset</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Period</th>
                        @if(auth()->user()->isAdmin())<th class="px-5 py-3">Manager</th>@endif
                        <th class="px-5 py-3 text-right">Revenue</th>
                        <th class="px-5 py-3 text-right">New</th>
                        <th class="px-5 py-3 text-right">Returning</th>
                        <th class="px-5 py-3 text-right">Expenses</th>
                        <th class="px-5 py-3">Submitted</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $report->type === 'weekly' ? 'bg-amber-500' : 'bg-slate-500' }}"></span>
                                {{ ucfirst($report->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $report->periodLabel() }}</td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-[10px] font-bold">{{ strtoupper(substr($report->user->name, 0, 1)) }}</div>
                                <span class="text-xs text-gray-600">{{ $report->user->name }}</span>
                            </div>
                        </td>
                        @endif
                        <td class="px-5 py-3 text-right text-xs font-semibold text-gray-900">{{ $report->revenue_overall !== null ? number_format($report->revenue_overall) . ' TSh' : '—' }}</td>
                        <td class="px-5 py-3 text-right text-xs text-gray-600">{{ $report->new_clients ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-xs text-gray-600">{{ $report->returning_clients ?? '—' }}</td>
                        <td class="px-5 py-3 text-right text-xs text-red-600">{{ number_format($report->totalExpenses()) }} TSh</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('manager.reports.show', $report) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="View">
                                    <i class="hgi-stroke hgi-view text-base"></i>
                                </a>
                                <a href="{{ route('manager.reports.edit', $report) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                    <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                </a>
                                <form method="POST" action="{{ route('manager.reports.destroy', $report) }}" data-ajax data-confirm="Delete this {{ $report->type }} report?" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                        <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                <i class="hgi-stroke hgi-file-02 text-4xl text-gray-300"></i>
                                <p class="text-xs">No reports found</p>
                                <a href="{{ route('manager.reports.create') }}" class="text-[10px] font-medium text-slate-600 hover:text-slate-800">Create your first report</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
