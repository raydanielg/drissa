@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-ui.page-header section="Account" title="My Profile" description="Manage your personal information and password." />

    <x-ui.card class="p-0">
        {{-- Profile Banner --}}
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900">{{ $user->name }}</h1>
                    <x-ui.badge variant="secondary" class="mt-1">{{ ucfirst($user->roles->first()->name ?? 'User') }}</x-ui.badge>
                </div>
            </div>
        </div>

        <x-ui.card-content class="py-6">
            @if(session('status'))
                <x-ui.alert variant="emerald" class="mb-4">
                    <x-ui.alert-title>{{ session('status') }}</x-ui.alert-title>
                </x-ui.alert>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-ui.label for="name">Full Name</x-ui.label>
                        <x-ui.input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" />
                        @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="email">Email Address</x-ui.label>
                        <x-ui.input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" />
                        @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="phone">Phone Number</x-ui.label>
                        <x-ui.input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" />
                        @error('phone')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <x-ui.label for="role">Role</x-ui.label>
                        <x-ui.input id="role" type="text" value="{{ ucfirst($user->roles->first()->name ?? 'User') }}" disabled />
                    </div>
                </div>

                <x-ui.separator />

                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Change Password</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-ui.label for="password">New Password</x-ui.label>
                            <x-ui.input id="password" type="password" name="password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" />
                            @error('password')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-2">
                            <x-ui.label for="password_confirmation">Confirm Password</x-ui.label>
                            <x-ui.input id="password_confirmation" type="password" name="password_confirmation" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit" size="lg">Save Changes</x-ui.button>
                </div>
            </form>
        </x-ui.card-content>
    </x-ui.card>
</div>
@endsection
