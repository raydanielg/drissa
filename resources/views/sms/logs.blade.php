@extends('layouts.dashboard')

@section('title', 'SMS Logs - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Logs')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Sent Messages</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Time</th>
                    <th class="px-6 py-3">Recipient</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Message</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Gateway</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-3">{{ $log->recipient ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $log->phone }}</td>
                        <td class="px-6 py-3">{{ Str::limit($log->message, 60) }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($log->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ $log->status }}</span>
                        </td>
                        <td class="px-6 py-3">{{ $log->gateway ?? 'log' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No SMS logs yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
</div>
@endsection
