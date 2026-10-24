@php
    $allTypes = ['registration','appointment','visit','doctor','lab','pharmacy','payment','birthday','holiday','general','reminder','marketing'];
    $typeLabels = [
        'registration' => 'Registration', 'appointment' => 'Appointment', 'visit' => 'Visit',
        'doctor' => 'Doctor', 'lab' => 'Lab', 'pharmacy' => 'Pharmacy',
        'payment' => 'Payment', 'birthday' => 'Birthday', 'holiday' => 'Holiday',
        'general' => 'General', 'reminder' => 'Reminder', 'marketing' => 'Marketing',
    ];
    $typeColors = [
        'payment' => 'bg-amber-500',
        'holiday' => 'bg-red-500',
    ];
@endphp

<div id="kpiCards" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total</span>
            <i class="hgi-stroke hgi-message-01 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-md border border-emerald-200 p-4">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active</span>
            <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Categories</span>
            <i class="hgi-stroke hgi-tag-01 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ count($allTypes) }}</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inactive</span>
            <i class="hgi-stroke hgi-unavailable text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['inactive'] }}</p>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="templatesTable">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium text-left">#</th>
                    <th class="px-5 py-3 font-medium text-left">Name</th>
                    <th class="px-5 py-3 font-medium text-left">Type</th>
                    <th class="px-5 py-3 font-medium text-left">Subject</th>
                    <th class="px-5 py-3 font-medium text-left">Message</th>
                    <th class="px-5 py-3 font-medium text-left">Status</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($templates as $template)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $template->id }}</td>
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $template->name }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $typeColors[$template->type] ?? 'bg-slate-400' }}"></span>{{ $typeLabels[$template->type] ?? ucfirst($template->type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $template->subject ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500 max-w-xs">
                            <span class="line-clamp-2">{{ Str::limit($template->body, 80) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $template->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="editTemplate({{ $template->id }}, {{ json_encode($template->name) }}, {{ json_encode($template->body) }}, {{ json_encode($template->type) }}, {{ json_encode($template->subject ?? '') }}, {{ $template->is_active ? 1 : 0 }})" class="p-1.5 rounded text-slate-600 hover:bg-slate-100 transition-colors" title="Edit">
                                    <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                </button>
                                <button type="button" onclick="deleteTemplate({{ $template->id }}, {{ json_encode($template->name) }})" class="p-1.5 rounded text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                    <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">
                        <i class="hgi-stroke hgi-message-01 text-4xl mx-auto mb-2 text-gray-300"></i>
                        No templates found
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span>Showing</span>
            <span class="font-semibold text-gray-700">{{ $templates->firstItem() ?? 0 }}</span>
            <span>to</span>
            <span class="font-semibold text-gray-700">{{ $templates->lastItem() ?? 0 }}</span>
            <span>of</span>
            <span class="font-semibold text-gray-700">{{ $templates->total() }}</span>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.select id="perPageSelect" class="border border-gray-200 rounded px-2 py-1 text-xs focus:ring-2 focus:ring-slate-300 outline-none" onchange="changePerPage(this.value)">
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / page</option>
                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>All</option>
            </x-ui.select>
            {{ $templates->links() }}
        </div>
    </div>
</div>
