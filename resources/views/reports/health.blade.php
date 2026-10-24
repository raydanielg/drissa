@extends('layouts.dashboard')

@section('title', 'System Health - ' . config('app.name', 'Laravel'))
@section('page_title', 'System Health & Security Audit')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">System Health & Security Audit</h1>
    <p class="text-xs text-slate-400 mt-1">User account status and recent activity.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $users }}</p>
    </div>
    <div class="bg-white rounded-md border border-emerald-200 p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active Users</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeUsers }}</p>
    </div>
    <div class="bg-white rounded-md border border-red-200 p-4">
        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inactive Users</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $users - $activeUsers }}</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Recent Activity Logs</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">User</th>
                    <th class="px-5 py-3">Action</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $log->action }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $log->description }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No logs</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
