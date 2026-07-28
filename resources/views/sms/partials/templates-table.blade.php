@php
    $allTypes = ['registration','appointment','visit','doctor','lab','pharmacy','payment','birthday','holiday','general','reminder','marketing'];
    $typeLabels = [
        'registration' => 'Registration', 'appointment' => 'Appointment', 'visit' => 'Visit',
        'doctor' => 'Doctor', 'lab' => 'Lab', 'pharmacy' => 'Pharmacy',
        'payment' => 'Payment', 'birthday' => 'Birthday', 'holiday' => 'Holiday',
        'general' => 'General', 'reminder' => 'Reminder', 'marketing' => 'Marketing',
    ];
    $typeColors = [
        'registration' => 'bg-purple-100 text-purple-700',
        'appointment' => 'bg-blue-100 text-blue-700',
        'visit' => 'bg-indigo-100 text-indigo-700',
        'doctor' => 'bg-cyan-100 text-cyan-700',
        'lab' => 'bg-teal-100 text-teal-700',
        'pharmacy' => 'bg-emerald-100 text-emerald-700',
        'payment' => 'bg-amber-100 text-amber-700',
        'birthday' => 'bg-pink-100 text-pink-700',
        'holiday' => 'bg-red-100 text-red-700',
        'general' => 'bg-gray-100 text-gray-700',
        'reminder' => 'bg-orange-100 text-orange-700',
        'marketing' => 'bg-fuchsia-100 text-fuchsia-700',
    ];
@endphp

<div id="kpiCards" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active</span>
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Categories</span>
            <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-sky-600">{{ count($allTypes) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Inactive</span>
            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-500">{{ $stats['inactive'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="templatesTable">
            <thead>
                <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Subject</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Message</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templates as $template)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition-colors">
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $template->id }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900">{{ $template->name }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @php $color = $typeColors[$template->type] ?? 'bg-gray-100 text-gray-700'; @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $color }}">{{ $typeLabels[$template->type] ?? ucfirst($template->type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $template->subject ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs max-w-xs">
                            <span class="line-clamp-2">{{ Str::limit($template->body, 80) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($template->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="editTemplate({{ $template->id }}, {{ json_encode($template->name) }}, {{ json_encode($template->body) }}, {{ json_encode($template->type) }}, {{ json_encode($template->subject ?? '') }}, {{ $template->is_active ? 1 : 0 }})" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" onclick="deleteTemplate({{ $template->id }}, {{ json_encode($template->name) }})" class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
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
            <select id="perPageSelect" class="border border-gray-200 rounded-lg px-2 py-1 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" onchange="changePerPage(this.value)">
                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / page</option>
                <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>All</option>
            </select>
            {{ $templates->links() }}
        </div>
    </div>
</div>
