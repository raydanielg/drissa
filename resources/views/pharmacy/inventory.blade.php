@extends('layouts.dashboard')

@section('title', 'Inventory - ' . config('app.name', 'Laravel'))
@section('page_title', 'Medicine Inventory')

@section('content')
<div class="space-y-5">

    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Pharmacy</p>
                <h1 class="text-xl font-bold">Medicine Inventory</h1>
                <p class="text-xs text-slate-400 mt-1">Stock levels, pricing, and medication records.</p>
            </div>
            <button onclick="openAddDrawer()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-add-01 text-sm"></i>
                Add Medication
            </button>
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Medicines</span>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $medications->total() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Stock Value</span>
                <i class="hgi-stroke hgi-justice-scale-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">TSh {{ number_format($stockValue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Cost price total</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Retail Value</span>
                <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">TSh {{ number_format($retailValue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Sell price total</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Potential Profit</span>
                <i class="hgi-stroke hgi-trade-up text-base text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">TSh {{ number_format($potentialProfit, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">If all stock sold</p>
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
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white border-l-2 border-amber-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-alert-02 text-xl text-amber-600 flex-shrink-0"></i>
            <div>
                <p class="text-xs font-semibold text-gray-900">{{ $lowStock }} Low Stock</p>
                <p class="text-[10px] text-gray-500">Items at or below reorder level</p>
            </div>
        </div>
        <div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-cancel-01 text-xl text-red-600 flex-shrink-0"></i>
            <div>
                <p class="text-xs font-semibold text-gray-900">{{ $outOfStock }} Out of Stock</p>
                <p class="text-[10px] text-gray-500">Items with zero quantity</p>
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
    <div class="flex flex-col sm:flex-row justify-between gap-3">
        <div class="relative flex-1 max-w-xs">
            <i class="hgi-stroke hgi-search-01 text-base text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <x-ui.input type="text" id="medSearch" placeholder="Search medicines..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="medTable">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100 bg-gray-50">
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
                                    <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <i class="hgi-stroke hgi-test-tube-01 text-base text-slate-600"></i>
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
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $med->stock_quantity == 0 ? 'bg-red-500' : ($med->stock_quantity <= $med->reorder_level ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    {{ $med->stock_quantity }}
                                </span>
                                @if($med->stock_quantity <= $med->reorder_level)
                                    <span class="text-[9px] text-amber-600 block mt-0.5">Reorder at {{ $med->reorder_level }}</span>
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
                                    <button onclick="openEditDrawer({{ $med->id }})" class="p-1.5 rounded text-slate-600 hover:bg-slate-100 transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    </button>
                                    <button onclick="openDeleteModal({{ $med->id }}, '{{ addslashes($med->name) }}')" class="p-1.5 rounded text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-12 text-center text-gray-400">
                            <i class="hgi-stroke hgi-test-tube-01 text-4xl mx-auto mb-2 text-gray-300"></i>
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
<div id="drawerBackdrop" class="hidden fixed inset-0 z-40 bg-black/30 transition-opacity" onclick="closeDrawer()"></div>
<div id="drawer" class="fixed top-0 right-0 z-50 h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 overflow-y-auto">
    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
        <h3 id="drawerTitle" class="text-sm font-semibold text-gray-900">Add Medication</h3>
        <button onclick="closeDrawer()" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
        </button>
    </div>
    <form id="medForm" method="POST" class="p-6 space-y-5">
        @csrf
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                <x-ui.input type="text" name="name" id="f_name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Generic Name</label>
                <x-ui.input type="text" name="generic_name" id="f_generic" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Form</label>
                <x-ui.select name="form" id="f_form" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all">
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
                </x-ui.select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                <x-ui.input type="text" name="category" id="f_category" placeholder="e.g. Antibiotics" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch No.</label>
                <x-ui.input type="text" name="batch_no" id="f_batch" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Manufacturer</label>
                <x-ui.input type="text" name="manufacturer" id="f_manufacturer" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
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
        <div class="bg-gray-50 rounded-md p-4 space-y-3">
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Pricing</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Buy Price (TSh) *</label>
                    <x-ui.input type="number" name="purchase_price" id="f_purchase" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" oninput="calcProfit()" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Sell Price (TSh) *</label>
                    <x-ui.input type="number" name="unit_price" id="f_sell" step="0.01" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" oninput="calcProfit()" />
                </div>
            </div>
            <div id="profitDisplay" class="text-xs text-gray-500"></div>
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Stock Qty *</label>
                <x-ui.input type="number" name="stock_quantity" id="f_stock" required value="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level *</label>
                <x-ui.input type="number" name="reorder_level" id="f_reorder" required value="10" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                <x-ui.input type="text" data-fp-date name="expiry_date" id="f_expiry" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <x-ui.select name="is_active" id="f_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-ui.select>
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
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" id="f_desc" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all resize-none"></x-ui.textarea>
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
        <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
            <button type="button" onclick="closeDrawer()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded transition-colors">Cancel</button>
            <button type="submit" id="drawerSubmit" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">Save Medication</button>
        </div>
    </form>
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
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-md shadow-xl w-full max-w-sm mx-4">
        <div class="p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="hgi-stroke hgi-delete-02 text-2xl text-red-600"></i>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Delete Medication?</h3>
            <p class="text-xs text-gray-500 mb-1">Are you sure you want to delete</p>
            <p id="deleteName" class="text-sm font-medium text-gray-900 mb-5"></p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 text-sm text-gray-700 border border-gray-200 rounded hover:bg-gray-50 transition-colors">Cancel</button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors">Delete</button>
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
