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

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'doctors' => User::role('doctor')->count(),
            'reception' => User::role('reception')->count(),
            'admin' => User::role('admin')->count(),
        ];

        if (request()->wantsJson()) {
            return response()->json(['roles' => $roles]);
        }

        return view('users.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::pluck('name');
        if (request()->wantsJson()) {
            return response()->json(['roles' => $roles]);
        }
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
        $user->load('roles');

        ActivityLog::log('user_created', $user, "Created user {$user->name}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User created successfully.', 'user' => $user]);
        }

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name');
        if (request()->wantsJson()) {
            return response()->json(['user' => $user, 'roles' => $roles]);
        }
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
        $user->load('roles');

        ActivityLog::log('user_updated', $user, "Updated user {$user->name}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully.', 'user' => $user]);
        }

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        ActivityLog::log('user_deleted', $user, "Deleted user {$user->name}");
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        }

        return back()->with('status', 'User deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (in_array(auth()->id(), $ids)) {
            return response()->json(['success' => false, 'message' => 'You cannot delete yourself.'], 400);
        }

        $count = User::whereIn('id', $ids)->delete();
        ActivityLog::log('bulk_users_deleted', null, "Deleted {$count} users");

        return response()->json(['success' => true, 'message' => "Deleted {$count} users successfully."]);
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (in_array(auth()->id(), $ids)) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate yourself.'], 400);
        }

        $count = User::whereIn('id', $ids)->update(['is_active' => false]);
        ActivityLog::log('bulk_users_deactivated', null, "Deactivated {$count} users");

        return response()->json(['success' => true, 'message' => "Deactivated {$count} users successfully."]);
    }

    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        $count = User::whereIn('id', $ids)->update(['is_active' => true]);
        ActivityLog::log('bulk_users_activated', null, "Activated {$count} users");

        return response()->json(['success' => true, 'message' => "Activated {$count} users successfully."]);
    }
}
