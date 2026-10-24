@extends('layouts.dashboard')

@section('title', 'Edit User - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Medical Staff')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">Edit Medical Staff</h1>
        <p class="text-xs text-slate-400 mt-1">Update account details for {{ $user->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                <x-ui.input type="text" name="name" placeholder="e.g. Dr. Amina Juma" value="{{ $user->name }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                <x-ui.input type="email" name="email" placeholder="e.g. staff@clinic.co.tz" value="{{ $user->email }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                <x-ui.input type="text" name="phone" placeholder="e.g. 0678 000 000" value="{{ $user->phone }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                <x-ui.select name="role" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">New Password (leave blank to keep current)</label>
                <x-ui.input type="password" name="password" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                <label class="text-sm text-gray-700">Active account</label>
            </div>
            <div class="pt-4">
                <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
