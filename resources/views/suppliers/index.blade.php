@extends('layouts.dashboard')

@section('title', 'Suppliers - ' . config('app.name', 'Laravel'))
@section('page_title', 'Enterprise & Logistics - Suppliers')

@section('content')
<div class="space-y-5">
<div class="bg-slate-800 rounded-md p-6 text-white flex items-center justify-between">
    <div>
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Logistics</p>
        <h1 class="text-xl font-bold">Suppliers</h1>
        <p class="text-xs text-slate-400 mt-1">Manage pharmacy suppliers and contacts.</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="bg-white text-slate-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-slate-100">+ Add Supplier</a>
</div>
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">All Suppliers</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium text-left">Name</th>
                    <th class="px-5 py-3 font-medium text-left">Contact Person</th>
                    <th class="px-5 py-3 font-medium text-left">Phone</th>
                    <th class="px-5 py-3 font-medium text-left">Email</th>
                    <th class="px-5 py-3 font-medium text-left">Status</th>
                    <th class="px-5 py-3 font-medium text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($suppliers as $supplier)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $supplier->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $supplier->contact_person ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $supplier->email ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $supplier->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 flex items-center gap-2">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-slate-600 hover:text-slate-800 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" data-ajax data-confirm="Delete this supplier?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-6 text-center text-gray-400 text-xs">No suppliers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $suppliers->links() }}
    </div>
</div>
</div>
@endsection
