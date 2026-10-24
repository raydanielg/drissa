@extends('layouts.dashboard')

@section('title', 'Audit Logs - ' . config('app.name', 'Laravel'))
@section('page_title', 'Security Audit Logs')

@section('content')
<div class="space-y-5">
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Security</p>
    <h1 class="text-xl font-bold">Audit Logs</h1>
    <p class="text-xs text-slate-400 mt-1">Track user actions and system events.</p>
</div>
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Recent Activity</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium text-left">Time</th>
                    <th class="px-5 py-3 font-medium text-left">User</th>
                    <th class="px-5 py-3 font-medium text-left">Action</th>
                    <th class="px-5 py-3 font-medium text-left">Description</th>
                    <th class="px-5 py-3 font-medium text-left">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs text-gray-600 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3"><span class="text-[10px] font-medium text-slate-600">{{ $log->action }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $log->description }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500 font-mono">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No activity logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
</div>
@endsection
