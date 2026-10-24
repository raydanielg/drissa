@extends('layouts.dashboard')

@section('title', 'SMS Logs - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Logs')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">SMS Logs</h1>
        <p class="text-xs text-slate-400 mt-1">Messages sent through the SMS gateway.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Sent Messages</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-5 py-3">Recipient</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Message</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Gateway</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $log->recipient ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $log->phone }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ Str::limit($log->message, 60) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $log->status === 'sent' ? 'bg-emerald-500' : ($log->status === 'failed' ? 'bg-red-500' : 'bg-amber-500') }}"></span>{{ $log->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $log->gateway ?? 'log' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No SMS logs yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
