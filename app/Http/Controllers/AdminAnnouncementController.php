<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator:id,name')
            ->orderByDesc('is_active')
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = $request->user()->id;

        Announcement::create($data);

        return back()->with('success', 'Announcement published.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->validateData($request));

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'body' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'in:info,success,warning,critical'],
            'action_label' => ['nullable', 'string', 'max:60'],
            'action_url' => ['nullable', 'string', 'max:500'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['dismissible'] = $request->boolean('dismissible');
        $data['priority'] = (int) ($data['priority'] ?? 0);

        return $data;
    }
}
