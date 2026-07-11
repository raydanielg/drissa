<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        $roles = Role::pluck('name');
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::pluck('name');
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);
        $user->assignRole($data['role']);

        ActivityLog::log('user_created', $user, "Created user {$user->name}");

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $user->update($data);
        $user->syncRoles($data['role']);

        ActivityLog::log('user_updated', $user, "Updated user {$user->name}");

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        ActivityLog::log('user_deleted', $user, "Deleted user {$user->name}");
        $user->delete();

        return back()->with('status', 'User deleted.');
    }
}
