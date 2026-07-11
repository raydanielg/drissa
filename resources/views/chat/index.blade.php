@extends('layouts.dashboard')

@section('title', 'Team Chat - ' . config('app.name', 'Laravel'))
@section('page_title', 'Team Chat')

@section('content')
<div class="flex h-[calc(100vh-140px)] bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Sidebar --}}
    <div class="w-72 border-r border-gray-100 flex flex-col">
        <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Conversations</h2>
            <p class="text-xs text-gray-500 mt-1">Select a user to start chatting</p>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($conversations as $conversation)
                @php $unread = $conversation->unreadCountFor(Auth::user()); @endphp
                <a href="{{ route('chat.show', $conversation) }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors {{ $loop->first ? 'bg-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($conversation->titleFor(Auth::user()), 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 truncate">{{ $conversation->titleFor(Auth::user()) }}</span>
                            @if($unread > 0)
                                <span class="bg-gold-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
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
        <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">Start a conversation</h3>
        <p class="text-sm text-gray-500 mt-1">Select a user from the dropdown to begin</p>

        <form action="{{ route('chat.store') }}" method="POST" class="mt-6 flex gap-2" data-ajax>
            @csrf
            <select name="user_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm min-w-[200px]">
                <option value="">Choose user...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->getRoleNames()->first() ?? 'User') }})</option>
                @endforeach
            </select>
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Start Chat</button>
        </form>
    </div>
</div>
@endsection
