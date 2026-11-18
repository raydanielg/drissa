@extends('layouts.dashboard')

@section('title', 'Announcements - ' . config('app.name', 'Laravel'))
@section('page_title', 'Announcements')

@section('content')
<div class="space-y-5">
    <div class="brand-hero rounded-md p-6 text-white flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-white/60 mb-1">System</p>
            <h1 class="text-xl font-bold">Announcements</h1>
            <p class="text-xs text-white/70 mt-1">Publish banners shown at the top of every staff page.</p>
        </div>
    </div>

    {{-- Create / edit form --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h2 id="formTitle" class="text-sm font-bold text-gray-900 mb-4">New Announcement</h2>
        <form id="announcementForm" method="POST" action="{{ route('announcements.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="md:col-span-2">
                <label class="text-xs font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="fTitle" required maxlength="191" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="e.g. System maintenance on Sunday">
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-medium text-gray-700">Message</label>
                <textarea name="body" id="fBody" rows="2" maxlength="2000" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="Optional details shown under the title"></textarea>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Type</label>
                <select name="type" id="fType" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="info">Information (blue)</option>
                    <option value="success">Success (green)</option>
                    <option value="warning">Warning (amber)</option>
                    <option value="critical">Critical (red)</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Priority (higher shows first)</label>
                <input type="number" name="priority" id="fPriority" value="0" min="0" max="100" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Button label (optional)</label>
                <input type="text" name="action_label" id="fActionLabel" maxlength="60" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="e.g. Learn more">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Button link (optional)</label>
                <input type="text" name="action_url" id="fActionUrl" maxlength="500" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="https://... or /path">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Show from (optional)</label>
                <input type="datetime-local" name="starts_at" id="fStarts" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-700">Hide after (optional)</label>
                <input type="datetime-local" name="ends_at" id="fEnds" class="mt-1 w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div class="flex items-center gap-6 md:col-span-2">
                <label class="flex items-center gap-2 text-xs text-gray-700"><input type="checkbox" name="is_active" id="fActive" value="1" checked class="rounded"> Active</label>
                <label class="flex items-center gap-2 text-xs text-gray-700"><input type="checkbox" name="dismissible" id="fDismissible" value="1" checked class="rounded"> Users can dismiss</label>
            </div>
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 brand-btn text-white text-sm font-medium rounded">Publish</button>
                <button type="button" id="cancelEdit" class="hidden px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200" onclick="resetAnnouncementForm()">Cancel edit</button>
            </div>
        </form>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <clinic-table placeholder="Search announcements…">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Title</th>
                        <th class="px-5 py-3 font-medium text-left">Type</th>
                        <th class="px-5 py-3 font-medium text-left">Window</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($announcements as $a)
                        @php
                            $typeStyles = ['info' => 'bg-blue-100 text-blue-700', 'success' => 'bg-emerald-100 text-emerald-700', 'warning' => 'bg-amber-100 text-amber-700', 'critical' => 'bg-red-100 text-red-700'];
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <p class="text-xs font-medium text-gray-900">{{ $a->title }}</p>
                                @if ($a->body)
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ Str::limit($a->body, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3"><span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $typeStyles[$a->type] ?? $typeStyles['info'] }}">{{ ucfirst($a->type) }}</span></td>
                            <td class="px-5 py-3 text-[10px] text-gray-500">
                                {{ $a->starts_at?->format('d M H:i') ?? 'Now' }} → {{ $a->ends_at?->format('d M H:i') ?? 'Open' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $a->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                    {{ $a->is_active ? 'Active' : 'Off' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="text-xs text-blue-600 hover:underline" onclick="editAnnouncement(this)" data-a="{{ json_encode($a->only(['id','title','body','type','action_label','action_url','priority','is_active','dismissible']) + ['starts_at' => $a->starts_at?->format('Y-m-d\TH:i'), 'ends_at' => $a->ends_at?->format('Y-m-d\TH:i')]) }}">Edit</button>
                                    <form method="POST" action="{{ route('announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">No announcements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </clinic-table>
        @if ($announcements->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">{{ $announcements->links() }}</div>
        @endif
    </div>
</div>

<script>
function editAnnouncement(btn) {
    var a = JSON.parse(btn.dataset.a);
    var f = document.getElementById('announcementForm');
    f.action = '{{ url('announcements') }}/' + a.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formTitle').textContent = 'Edit Announcement #' + a.id;
    fTitle.value = a.title; fBody.value = a.body || ''; fType.value = a.type;
    fActionLabel.value = a.action_label || ''; fActionUrl.value = a.action_url || '';
    fPriority.value = a.priority; fStarts.value = a.starts_at || ''; fEnds.value = a.ends_at || '';
    fActive.checked = !!a.is_active; fDismissible.checked = !!a.dismissible;
    document.getElementById('cancelEdit').classList.remove('hidden');
    f.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
function resetAnnouncementForm() {
    var f = document.getElementById('announcementForm');
    f.action = '{{ route('announcements.store') }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formTitle').textContent = 'New Announcement';
    document.getElementById('cancelEdit').classList.add('hidden');
    f.reset(); fActive.checked = true; fDismissible.checked = true;
}
</script>
@endsection
