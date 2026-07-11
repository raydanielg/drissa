<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = User::role('doctor')->with('department');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $doctors = $query->latest()->paginate(20);
        $departments = Department::where('is_active', true)->get();

        $stats = [
            'total' => User::role('doctor')->count(),
            'active' => User::role('doctor')->where('is_active', true)->count(),
            'inactive' => User::role('doctor')->where('is_active', false)->count(),
            'departments' => $departments->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json(['departments' => $departments]);
        }

        return view('admin.doctors.index', compact('doctors', 'departments', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'required|string|min:8',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        $doctor = User::create($data);
        $doctor->assignRole('doctor');
        $doctor->load('department');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Doctor added successfully.', 'doctor' => $doctor]);
        }

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor added successfully.');
    }

    public function show(User $doctor)
    {
        abort_unless($doctor->hasRole('doctor'), 404);

        $doctor->load(['department', 'visitsAsDoctor.patient', 'appointmentsAsDoctor.patient']);

        $activeVisits = $doctor->visitsAsDoctor()
            ->whereIn('status', ['waiting', 'in_progress', 'doctor_assigned'])
            ->with('patient')
            ->latest('registered_at')
            ->get();

        $completedVisits = $doctor->visitsAsDoctor()
            ->whereIn('status', ['completed', 'payment', 'closed'])
            ->with('patient')
            ->latest('completed_at')
            ->limit(20)
            ->get();

        $upcomingAppointments = $doctor->appointmentsAsDoctor()
            ->where('scheduled_at', '>=', now())
            ->with('patient')
            ->latest('scheduled_at')
            ->limit(10)
            ->get();

        $stats = [
            'total_visits' => $doctor->visitsAsDoctor()->count(),
            'active_visits' => $activeVisits->count(),
            'completed_visits' => $doctor->visitsAsDoctor()->whereIn('status', ['completed', 'closed'])->count(),
            'appointments_today' => $doctor->appointmentsAsDoctor()->whereDate('scheduled_at', today())->count(),
        ];

        $departments = Department::where('is_active', true)->get();

        return view('admin.doctors.show', compact('doctor', 'activeVisits', 'completedVisits', 'upcomingAppointments', 'stats', 'departments'));
    }

    public function update(Request $request, User $doctor)
    {
        abort_unless($doctor->hasRole('doctor'), 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $doctor->update($data);
        $doctor->load('department');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Doctor updated successfully.', 'doctor' => $doctor]);
        }

        return back()->with('status', 'Doctor profile updated.');
    }

    public function destroy(User $doctor)
    {
        abort_unless($doctor->hasRole('doctor'), 404);

        $doctor->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Doctor deleted successfully.']);
        }

        return back()->with('status', 'Doctor deleted successfully.');
    }

    public function resetPassword(Request $request, User $doctor)
    {
        abort_unless($doctor->hasRole('doctor'), 404);

        // Auto-generate random password
        $newPassword = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'), 0, 8));

        $doctor->update(['password' => Hash::make($newPassword)]);

        // Send email notification
        \Mail::to($doctor->email)->send(new \App\Mail\PasswordResetNotification($doctor, $newPassword));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Password reset successfully. Email sent to ' . $doctor->email]);
        }

        return back()->with('status', 'Password reset successfully. Email sent to ' . $doctor->email);
    }

    public function toggleActive(User $doctor)
    {
        abort_unless($doctor->hasRole('doctor'), 404);

        $doctor->update(['is_active' => ! $doctor->is_active]);

        return back()->with('status', 'Doctor status updated.');
    }
}
