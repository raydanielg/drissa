@extends('layouts.dashboard')

@section('title', 'Audit Logs - ' . config('app.name', 'Laravel'))
@section('page_title', 'Security Audit Logs')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Recent Activity</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Time</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Action</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t border-gray-100">
                        <td class="px-6 py-3">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-3">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs">{{ $log->action }}</span></td>
                        <td class="px-6 py-3">{{ $log->description }}</td>
                        <td class="px-6 py-3">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No activity logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
@endsection
