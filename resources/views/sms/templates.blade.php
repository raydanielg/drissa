@extends('layouts.dashboard')

@section('title', 'SMS Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Templates')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Add Template</h2>
        <form method="POST" action="{{ route('sms.templates.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4" data-ajax>
            @csrf
            <div>
                <input type="text" name="name" placeholder="Template name" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option value="general">General</option>
                    <option value="appointment">Appointment</option>
                    <option value="payment">Payment</option>
                    <option value="lab">Lab</option>
                    <option value="birthday">Birthday</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <textarea name="body" placeholder="Message body" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3" required></textarea>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Template</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Existing Templates</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Body</th>
                        <th class="px-6 py-3">Active</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium">{{ $template->name }}</td>
                            <td class="px-6 py-3 capitalize">{{ $template->type }}</td>
                            <td class="px-6 py-3">{{ Str::limit($template->body, 50) }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $template->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">{{ $template->is_active ? 'Yes' : 'No' }}</span>
                            </td>
                            <td class="px-6 py-3">
                                <button type="button" onclick="editTemplate({{ $template->id }}, '{{ addslashes($template->name) }}', '{{ addslashes($template->body) }}', '{{ $template->type }}', {{ $template->is_active ? 1 : 0 }})" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</button>
                                <form method="POST" action="{{ route('sms.templates.destroy', $template) }}" data-ajax data-confirm="Delete this template?" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No templates yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $templates->links() }}</div>
    </div>
</div>

<div id="editTemplateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Template</h3>
        <form id="editTemplateForm" method="POST" class="space-y-4" data-ajax>
            @csrf
            @method('PUT')
            <input type="text" name="name" id="editName" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            <select name="type" id="editType" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="general">General</option>
                <option value="appointment">Appointment</option>
                <option value="payment">Payment</option>
                <option value="lab">Lab</option>
                <option value="birthday">Birthday</option>
            </select>
            <textarea name="body" id="editBody" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3" required></textarea>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="editActive" value="1" class="rounded border-gray-300">
                <label class="text-sm text-gray-700">Active</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Cancel</button>
                <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editTemplate(id, name, body, type, isActive) {
        const form = document.getElementById('editTemplateForm');
        form.action = '/sms/templates/' + id;
        document.getElementById('editName').value = name;
        document.getElementById('editBody').value = body;
        document.getElementById('editType').value = type;
        document.getElementById('editActive').checked = isActive === 1;
        document.getElementById('editTemplateModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
