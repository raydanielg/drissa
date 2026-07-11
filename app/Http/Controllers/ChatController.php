<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conversations = Auth::user()->conversations()
            ->with(['lastMessage.user', 'users'])
            ->latest('updated_at')
            ->get();

        $users = User::where('id', '!=', Auth::id())->get();

        return view('chat.index', compact('conversations', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $otherUser = User::findOrFail($data['user_id']);
        $currentUser = Auth::user();

        $existing = Conversation::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('user_id', $currentUser->id))
            ->whereHas('users', fn($q) => $q->where('user_id', $otherUser->id))
            ->first();

        if ($existing) {
            return redirect()->route('chat.show', $existing);
        }

        $conversation = Conversation::create([
            'created_by' => $currentUser->id,
            'type' => 'private',
        ]);

        $conversation->users()->attach([$currentUser->id, $otherUser->id]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $conversation->load(['users', 'messages.user']);

        $conversation->users()->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversations = Auth::user()->conversations()
            ->with(['lastMessage.user'])
            ->latest('updated_at')
            ->get();

        $users = User::where('id', '!=', Auth::id())->get();

        return view('chat.show', compact('conversation', 'conversations', 'users'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $data['body'],
            'type' => 'text',
        ]);

        $conversation->touch();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function poll(Conversation $conversation, Request $request)
    {
        $this->authorizeAccess($conversation);

        $afterId = $request->input('after_id', 0);

        $messages = $conversation->messages()
            ->with('user')
            ->where('id', '>', $afterId)
            ->where('user_id', '!=', Auth::id())
            ->get();

        Message::whereIn('id', $messages->pluck('id'))->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    public function unreadCount()
    {
        $total = 0;
        foreach (Auth::user()->conversations as $conversation) {
            $total += $conversation->unreadCountFor(Auth::user());
        }
        return response()->json(['count' => $total]);
    }

    protected function authorizeAccess(Conversation $conversation): void
    {
        abort_unless($conversation->users()->where('user_id', Auth::id())->exists(), 403);
    }
}
