<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function active()
    {
        return Announcement::active()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->limit(3)
            ->get(['id', 'title', 'body', 'type', 'action_label', 'action_url', 'dismissible', 'priority']);
    }
}
