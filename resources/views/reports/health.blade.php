@extends('layouts.dashboard')

@section('title', 'System Health - ' . config('app.name', 'Laravel'))
@section('page_title', 'System Health & Security Audit')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Total Users</p>
        <p class="text-2xl font-bold text-gray-900">{{ $users }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Active Users</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $activeUsers }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Inactive Users</p>
        <p class="text-2xl font-bold text-red-600">{{ $users - $activeUsers }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Recent Activity Logs</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Action</th>
                    <th class="px-6 py-3">Description</th>
                    <th class="px-6 py-3">Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-700">{{ $log->action }}</span></td>
                        <td class="px-6 py-3">{{ $log->description }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
