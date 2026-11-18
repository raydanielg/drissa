<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationFeedController extends Controller
{
    /**
     * Unified feed for <clinic-notifications>: per-user notifications merged
     * with active system announcements.
     */
    public function feed(Request $request)
    {
        $userId = $request->user()->id;

        $userItems = UserNotification::where('user_id', $userId)
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn ($n) => [
                'id' => 'u-' . $n->id,
                'kind' => 'user',
                'title' => $n->title,
                'body' => $n->body,
                'type' => $n->type,
                'url' => $n->url,
                'read' => (bool) $n->read_at,
                'at' => $n->created_at?->toIso8601String(),
            ]);

        $announcements = Announcement::active()
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'id' => 'a-' . $a->id,
                'kind' => 'announcement',
                'title' => $a->title,
                'body' => $a->body,
                'type' => $a->type,
                'url' => $a->action_url,
                'read' => false, // read state tracked client-side per browser
                'at' => $a->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'items' => $userItems->concat($announcements)
                ->sortByDesc('at')
                ->values(),
            'unread' => UserNotification::where('user_id', $userId)->unread()->count(),
        ]);
    }

    public function markRead(Request $request, UserNotification $notification)
    {
        abort_if($notification->user_id !== $request->user()->id, 403);
        $notification->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)->unread()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
