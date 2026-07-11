@extends('layouts.dashboard')

@section('title', 'Chat - ' . config('app.name', 'Laravel'))
@section('page_title', 'Chat with ' . $conversation->titleFor(Auth::user()))

@section('content')
<div class="flex h-[calc(100vh-140px)] bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Conversations list --}}
    <div class="w-72 border-r border-gray-100 flex flex-col hidden md:flex">
        <div class="p-4 border-b border-gray-100">
            <a href="{{ route('chat.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">&larr; All chats</a>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($conversations as $conv)
                @php $unread = $conv->unreadCountFor(Auth::user()); @endphp
                <a href="{{ route('chat.show', $conv) }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors {{ $conv->id === $conversation->id ? 'bg-emerald-50 border-l-2 border-emerald-500' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr($conv->titleFor(Auth::user()), 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900 truncate">{{ $conv->titleFor(Auth::user()) }}</span>
                            @if($unread > 0)
                                <span class="bg-gold-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($conv->lastMessage?->body ?? 'No messages yet', 25) }}</p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-sm text-gray-400">No conversations</div>
            @endforelse
        </div>
    </div>

    {{-- Chat area --}}
    <div class="flex-1 flex flex-col bg-gray-50/50">
        <div class="p-4 border-b border-gray-100 bg-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr($conversation->titleFor(Auth::user()), 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $conversation->titleFor(Auth::user()) }}</h3>
                    <p class="text-xs text-gray-500">{{ $conversation->type === 'private' ? 'Private chat' : 'Group chat' }}</p>
                </div>
            </div>
        </div>

        <div id="messagesArea" class="flex-1 overflow-y-auto p-4 space-y-3">
            @foreach($conversation->messages as $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm {{ $message->user_id === Auth::id() ? 'bg-emerald-600 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none' }}">
                        <p>{{ $message->body }}</p>
                        <div class="text-[10px] mt-1 opacity-70 flex items-center justify-end gap-1">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->user_id === Auth::id())
                                <span>{{ $message->isRead() ? '✓✓' : '✓' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-white border-t border-gray-100">
            <form id="chatForm" method="POST" action="{{ route('chat.send', $conversation) }}" class="flex gap-2">
                @csrf
                <input type="text" id="chatInput" name="body" class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Type a message..." required autocomplete="off">
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const area = document.getElementById('messagesArea');
    const conversationId = {{ $conversation->id }};
    let lastId = {{ $conversation->messages->last()?->id ?? 0 }};

    function scrollToBottom() {
        area.scrollTop = area.scrollHeight;
    }
    scrollToBottom();

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const body = input.value.trim();
        if (!body) return;

        const formData = new FormData(form);
        input.value = '';

        // Optimistically append own message
        const now = new Date();
        const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        const msgDiv = document.createElement('div');
        msgDiv.className = 'flex justify-end';
        msgDiv.innerHTML = `<div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm bg-emerald-600 text-white rounded-br-none"><p>${escapeHtml(body)}</p><div class="text-[10px] mt-1 opacity-70 flex items-center justify-end gap-1">${time} <span>✓</span></div></div>`;
        area.appendChild(msgDiv);
        scrollToBottom();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value },
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.message) lastId = Math.max(lastId, data.message.id);
        } catch (err) {
            console.error(err);
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Poll for new messages every 5 seconds
    setInterval(async () => {
        try {
            const res = await fetch(`/chat/${conversationId}/poll?after_id=${lastId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (data.messages && data.messages.length) {
                data.messages.forEach(msg => {
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const isMe = msg.user_id === {{ Auth::id() }};
                    const div = document.createElement('div');
                    div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
                    div.innerHTML = `<div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm ${isMe ? 'bg-emerald-600 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none'}"><p>${escapeHtml(msg.body)}</p><div class="text-[10px] mt-1 opacity-70 flex items-center justify-end gap-1">${time}</div></div>`;
                    area.appendChild(div);
                    lastId = Math.max(lastId, msg.id);
                });
                scrollToBottom();
            }
        } catch (e) { console.error(e); }
    }, 5000);
})();
</script>
@endpush
@endsection
