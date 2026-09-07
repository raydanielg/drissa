@extends('layouts.dashboard')

@section('title', 'Inventory - ' . config('app.name', 'Laravel'))
@section('page_title', 'Medicine Inventory')

@section('content')
<div class="space-y-6">

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Financial Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Medicines</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $medications->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</span>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($stockValue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Cost price total</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Retail Value</span>
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-violet-600">TSh {{ number_format($retailValue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Sell price total</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Profit</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($potentialProfit, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">If all stock sold</p>
        </div>
    </div>

    {{-- Stock Alerts --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-amber-900">{{ $lowStock }} Low Stock</p>
                <p class="text-xs text-amber-700">Items at or below reorder level</p>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-red-900">{{ $outOfStock }} Out of Stock</p>
                <p class="text-xs text-red-700">Items with zero quantity</p>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <div class="relative flex-1 max-w-xs">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="medSearch" placeholder="Search medicines..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <button onclick="openAddDrawer()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Medication
        </button>
    </div>

    {{-- Medications Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="medTable">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 font-medium">Manufacturer</th>
                        <th class="px-5 py-3 font-medium">Batch</th>
                        <th class="px-5 py-3 font-medium">Stock</th>
                        <th class="px-5 py-3 font-medium">Buy Price</th>
                        <th class="px-5 py-3 font-medium">Sell Price</th>
                        <th class="px-5 py-3 font-medium">Profit/Unit</th>
                        <th class="px-5 py-3 font-medium">Expiry</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medications as $med)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors" data-name="{{ strtolower($med->name) }}">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $med->name }}</p>
                                        @if($med->generic_name)
                                            <p class="text-[10px] text-gray-400">{{ $med->generic_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if($med->category)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">{{ $med->category }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->manufacturer ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $med->batch_no ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $med->stock_quantity == 0 ? 'bg-red-100 text-red-700' : ($med->stock_quantity <= $med->reorder_level ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    {{ $med->stock_quantity }}
                                </span>
                                @if($med->stock_quantity <= $med->reorder_level)
                                    <span class="text-[9px] text-amber-500 block mt-0.5">Reorder at {{ $med->reorder_level }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">TSh {{ number_format($med->purchase_price, 0) }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">TSh {{ number_format($med->unit_price, 0) }}</td>
                            <td class="px-5 py-3">
                                @php $profit = $med->unit_price - $med->purchase_price; @endphp
                                <span class="font-medium {{ $profit > 0 ? 'text-emerald-600' : 'text-red-600' }}">TSh {{ number_format($profit, 0) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if($med->expiry_date)
                                    @php $isExpired = $med->expiry_date < now(); $isExpiring = $med->expiry_date < now()->addDays(30) && $med->expiry_date > now(); @endphp
                                    <span class="text-xs {{ $isExpired ? 'text-red-600 font-medium' : ($isExpiring ? 'text-amber-600 font-medium' : 'text-gray-500') }}">
                                        {{ $med->expiry_date->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="openEditDrawer({{ $med->id }})" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button onclick="openDeleteModal({{ $med->id }}, '{{ addslashes($med->name) }}')" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                            No medications found. Click "Add Medication" to get started.
                        </td></tr>
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

{{-- Slide-over Drawer --}}
<div id="drawerBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 transition-opacity" onclick="closeDrawer()"></div>
<div id="drawer" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 overflow-y-auto">
    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
        <h3 id="drawerTitle" class="text-sm font-semibold text-gray-900">Add Medication</h3>
        <button onclick="closeDrawer()" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <form id="medForm" method="POST" class="p-6 space-y-5">
        @csrf
        {{-- Name & Generic --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" id="f_name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Generic Name</label>
                <input type="text" name="generic_name" id="f_generic" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
        </div>
        {{-- Form & Category --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Form</label>
                <select name="form" id="f_form" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="">Select form...</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Capsule">Capsule</option>
                    <option value="Syrup">Syrup</option>
                    <option value="Injection">Injection</option>
                    <option value="Cream">Cream</option>
                    <option value="Ointment">Ointment</option>
                    <option value="Drops">Drops</option>
                    <option value="Inhaler">Inhaler</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                <input type="text" name="category" id="f_category" placeholder="e.g. Antibiotics" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
        </div>
        {{-- Batch & Manufacturer --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch No.</label>
                <input type="text" name="batch_no" id="f_batch" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Manufacturer</label>
                <input type="text" name="manufacturer" id="f_manufacturer" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
        </div>
        {{-- Pricing --}}
        <div class="bg-gray-50 rounded-xl p-4 space-y-3">
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Pricing</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Buy Price (TSh) *</label>
                    <input type="number" name="purchase_price" id="f_purchase" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" oninput="calcProfit()">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sell Price (TSh) *</label>
                    <input type="number" name="unit_price" id="f_sell" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" oninput="calcProfit()">
                </div>
            </div>
            <div id="profitDisplay" class="text-xs text-gray-500"></div>
        </div>
        {{-- Stock --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Stock Qty *</label>
                <input type="number" name="stock_quantity" id="f_stock" required value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level *</label>
                <input type="number" name="reorder_level" id="f_reorder" required value="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
        </div>
        {{-- Expiry & Active --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" id="f_expiry" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" id="f_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        {{-- Description --}}
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="f_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all resize-none"></textarea>
        </div>
        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
            <button type="button" onclick="closeDrawer()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
            <button type="submit" id="drawerSubmit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">Save Medication</button>
        </div>
    </form>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Delete Medication?</h3>
            <p class="text-xs text-gray-500 mb-1">Are you sure you want to delete</p>
            <p id="deleteName" class="text-sm font-medium text-gray-900 mb-5"></p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openAddDrawer() {
    document.getElementById('drawerTitle').textContent = 'Add Medication';
    document.getElementById('drawerSubmit').textContent = 'Save Medication';
    document.getElementById('medForm').action = '{{ route("pharmacy.medications.store") }}';
    document.getElementById('medForm').removeAttribute('_method');
    // Reset form
    ['f_name','f_generic','f_batch','f_manufacturer','f_category','f_desc','f_expiry'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f_form').value = '';
    document.getElementById('f_stock').value = '0';
    document.getElementById('f_reorder').value = '10';
    document.getElementById('f_purchase').value = '';
    document.getElementById('f_sell').value = '';
    document.getElementById('f_active').value = '1';
    calcProfit();
    showDrawer();
}

function openEditDrawer(id) {
    fetch(`{{ route("pharmacy.medications.edit", ":id") }}`.replace(':id', id), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const m = data.medication;
        document.getElementById('drawerTitle').textContent = 'Edit Medication';
        document.getElementById('drawerSubmit').textContent = 'Update Medication';
        document.getElementById('medForm').action = '{{ route("pharmacy.medications.update", ":id") }}'.replace(':id', id);
        // Add PUT method
        let methodInput = document.getElementById('medForm').querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            document.getElementById('medForm').appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        document.getElementById('f_name').value = m.name || '';
        document.getElementById('f_generic').value = m.generic_name || '';
        document.getElementById('f_form').value = m.form || '';
        document.getElementById('f_batch').value = m.batch_no || '';
        document.getElementById('f_manufacturer').value = m.manufacturer || '';
        document.getElementById('f_category').value = m.category || '';
        document.getElementById('f_desc').value = m.description || '';
        document.getElementById('f_stock').value = m.stock_quantity;
        document.getElementById('f_reorder').value = m.reorder_level;
        document.getElementById('f_purchase').value = m.purchase_price;
        document.getElementById('f_sell').value = m.unit_price;
        document.getElementById('f_expiry').value = m.expiry_date ? m.expiry_date.split('T')[0] : '';
        document.getElementById('f_active').value = m.is_active ? '1' : '0';
        calcProfit();
        showDrawer();
    });
}

function showDrawer() {
    document.getElementById('drawerBackdrop').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('drawer').classList.remove('translate-x-full');
    }, 10);
}

function closeDrawer() {
    document.getElementById('drawer').classList.add('translate-x-full');
    setTimeout(() => {
        document.getElementById('drawerBackdrop').classList.add('hidden');
    }, 300);
}

function calcProfit() {
    const buy = parseFloat(document.getElementById('f_purchase').value) || 0;
    const sell = parseFloat(document.getElementById('f_sell').value) || 0;
    const profit = sell - buy;
    const stock = parseInt(document.getElementById('f_stock').value) || 0;
    const display = document.getElementById('profitDisplay');
    if (buy > 0 || sell > 0) {
        const color = profit > 0 ? 'text-emerald-600' : 'text-red-600';
        const totalProfit = profit * stock;
        display.innerHTML = `<span class="${color} font-medium">Profit/Unit: TSh ${numberFormat(profit)}</span> <span class="text-gray-400">| Total: TSh ${numberFormat(totalProfit)}</span>`;
    } else {
        display.innerHTML = '';
    }
}

function numberFormat(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

function openDeleteModal(id, name) {
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteForm').action = '{{ route("pharmacy.medications.destroy", ":id") }}'.replace(':id', id);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Live search
document.getElementById('medSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('#medTable tbody tr').forEach(row => {
        const name = row.dataset.name || '';
        row.style.display = name.includes(term) ? '' : 'none';
    });
});

// Close drawer on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDrawer();
        closeDeleteModal();
    }
});
</script>
@endsection
