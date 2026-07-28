@extends('layouts.dashboard')

@section('title', 'Inventory - ' . config('app.name', 'Laravel'))
@section('page_title', 'Medicine Inventory')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Medicines</p>
            <p class="text-2xl font-bold text-gray-900">{{ $medications->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Low Stock</p>
            <p class="text-2xl font-bold text-amber-600">{{ $lowStock }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Out of Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ $outOfStock }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Stock Value</p>
            <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalValue, 0) }}</p>
        </div>
    </div>

    {{-- Add Medication Button --}}
    <div class="flex justify-end">
        <button onclick="document.getElementById('addMedModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Medication
        </button>
    </div>

    {{-- Medications Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Generic</th>
                        <th class="px-5 py-3 font-medium">Form</th>
                        <th class="px-5 py-3 font-medium">Stock</th>
                        <th class="px-5 py-3 font-medium">Reorder Level</th>
                        <th class="px-5 py-3 font-medium">Unit Price</th>
                        <th class="px-5 py-3 font-medium">Expiry</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medications as $med)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $med->name }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->generic_name ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->form ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $med->stock_quantity == 0 ? 'bg-red-100 text-red-700' : ($med->stock_quantity <= $med->reorder_level ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $med->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->reorder_level }}</td>
                            <td class="px-5 py-3 text-gray-500">TSh {{ number_format($med->unit_price, 0) }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->expiry_date?->format('M j, Y') ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <button onclick="editMed({{ $med->id }}, '{{ addslashes($med->name) }}', '{{ addslashes($med->generic_name ?? '') }}', '{{ addslashes($med->form ?? '') }}', {{ $med->stock_quantity }}, {{ $med->reorder_level }}, {{ $med->unit_price }}, '{{ $med->expiry_date?->format('Y-m-d') ?? '' }}', {{ $med->is_active ? 'true' : 'false' }})" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</button>
                                <form action="{{ route('pharmacy.medications.destroy', $med) }}" method="POST" class="inline" onsubmit="return confirm('Delete this medication?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium ml-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">No medications found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($medications->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $medications->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Add Medication Modal --}}
<div id="addMedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Add Medication</h3>
            <button onclick="document.getElementById('addMedModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form action="{{ route('pharmacy.medications.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Name *</label><input type="text" name="name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Generic Name</label><input type="text" name="generic_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Form</label><input type="text" name="form" placeholder="Tablet, Syrup..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Unit Price *</label><input type="number" name="unit_price" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock Qty *</label><input type="number" name="stock_quantity" required value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level *</label><input type="number" name="reorder_level" required value="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label><input type="date" name="expiry_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Active</label><select name="is_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addMedModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Medication Modal --}}
<div id="editMedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Edit Medication</h3>
            <button onclick="document.getElementById('editMedModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="editMedForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Name *</label><input type="text" name="name" id="edit_name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Generic Name</label><input type="text" name="generic_name" id="edit_generic" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Form</label><input type="text" name="form" id="edit_form" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Unit Price *</label><input type="number" name="unit_price" id="edit_price" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Stock Qty *</label><input type="number" name="stock_quantity" id="edit_stock" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level *</label><input type="number" name="reorder_level" id="edit_reorder" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label><input type="date" name="expiry_date" id="edit_expiry" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Active</label><select name="is_active" id="edit_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="1">Yes</option><option value="0">No</option></select></div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editMedModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMed(id, name, generic, form, stock, reorder, price, expiry, active) {
    document.getElementById('editMedForm').action = '{{ route("pharmacy.medications.update", ":id") }}'.replace(':id', id);
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_generic').value = generic;
    document.getElementById('edit_form').value = form;
    document.getElementById('edit_stock').value = stock;
    document.getElementById('edit_reorder').value = reorder;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_expiry').value = expiry;
    document.getElementById('edit_active').value = active ? '1' : '0';
    document.getElementById('editMedModal').classList.remove('hidden');
}
</script>
@endsection
