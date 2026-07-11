@extends('layouts.dashboard')

@section('title', 'Lab - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Queue')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Pending Lab Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Order ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Tests</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">#{{ $order->id }}</td>
                            <td class="px-6 py-3">{{ $order->visit->patient->fullName() }}</td>
                            <td class="px-6 py-3">{{ $order->items->pluck('labTest.name')->join(', ') }}</td>
                            <td class="px-6 py-3 space-y-2">
                                <form method="POST" action="{{ route('lab.orders.start', $order) }}">
                                    @csrf
                                    <button type="submit" class="bg-sky-500 text-white text-xs font-medium px-3 py-1 rounded-lg">Start Processing</button>
                                </form>

                                <form method="POST" action="{{ route('lab.orders.results', $order) }}" enctype="multipart/form-data" class="border rounded-lg p-3 bg-gray-50 space-y-2">
                                    @csrf
                                    <input type="text" name="results[0][parameter]" placeholder="Parameter" class="w-full border rounded-lg px-2 py-1 text-xs" required>
                                    <input type="text" name="results[0][value]" placeholder="Value" class="w-full border rounded-lg px-2 py-1 text-xs" required>
                                    <input type="text" name="results[0][unit]" placeholder="Unit" class="w-full border rounded-lg px-2 py-1 text-xs">
                                    <input type="text" name="results[0][reference_range]" placeholder="Reference range" class="w-full border rounded-lg px-2 py-1 text-xs">
                                    <select name="results[0][flag]" class="w-full border rounded-lg px-2 py-1 text-xs">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                    <input type="file" name="report" class="w-full text-xs">
                                    <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg">Submit Results</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No pending lab orders</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
