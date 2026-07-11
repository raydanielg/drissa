<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::latest()->paginate(20);

        $stats = [
            'total' => Department::count(),
            'active' => Department::where('is_active', true)->count(),
            'inactive' => Department::where('is_active', false)->count(),
        ];

        if (request()->wantsJson()) {
            return response()->json(['departments' => $departments]);
        }

        return view('departments.index', compact('departments', 'stats'));
    }

    public function create()
    {
        if (request()->wantsJson()) {
            return response()->json([]);
        }
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $department = Department::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Department created.', 'department' => $department]);
        }

        return redirect()->route('departments.index')->with('status', 'Department created.');
    }

    public function edit(Department $department)
    {
        if (request()->wantsJson()) {
            return response()->json(['department' => $department]);
        }
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $department->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Department updated.', 'department' => $department]);
        }

        return redirect()->route('departments.index')->with('status', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('status', 'Department deleted.');
    }
}
