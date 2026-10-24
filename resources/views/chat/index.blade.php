@extends('layouts.dashboard')

@section('title', 'Team Chat - ' . config('app.name', 'Laravel'))
@section('page_title', 'Team Chat')

@section('content')
<div class="flex h-[calc(100vh-140px)] bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Empty chat area --}}
    <div class="w-72 border-r border-gray-100 flex flex-col">
        <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Conversations</h2>
            <p class="text-xs text-gray-500 mt-1">Select a user to start chatting</p>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($conversations as $conversation)
                @php $unread = $conversation->unreadCountFor(Auth::user()); @endphp
                <a href="{{ route('chat.show', $conversation) }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors {{ $loop->first ? 'bg-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 text-sm font-bold">
                        {{ strtoupper(substr($conversation->titleFor(Auth::user()), 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 truncate">{{ $conversation->titleFor(Auth::user()) }}</span>
                            @if($unread > 0)
                                <span class="bg-slate-800 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $conversation->lastMessage?->user?->name ?? '' }}: {{ Str::limit($conversation->lastMessage?->body ?? 'No messages yet', 25) }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-sm text-gray-400">No conversations yet</div>
            @endforelse
        </div>
    </div>

    {{-- Empty chat area --}}
    <div class="flex-1 flex flex-col items-center justify-center bg-gray-50/50">
        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <i class="hgi-stroke hgi-bubble-chat text-base text-slate-600"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">Start a conversation</h3>
        <p class="text-sm text-gray-500 mt-1">Select a user from the dropdown to begin</p>

        <form action="{{ route('chat.store') }}" method="POST" class="mt-6 flex gap-2" data-ajax>
            @csrf
            <x-ui.select name="user_id" class="border border-gray-200 rounded px-3 py-2 text-sm min-w-[200px] focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="">Choose user...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->getRoleNames()->first() ?? 'User') }})</option>
                @endforeach
            </x-ui.select>
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Start Chat</button>
        </form>
    </div>
</div>
@endsection
