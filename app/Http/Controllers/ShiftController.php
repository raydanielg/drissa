<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $shifts = Shift::latest()->paginate(20);
        
        $stats = [
            'total' => Shift::count(),
            'active' => Shift::where('is_active', true)->count(),
            'inactive' => Shift::where('is_active', false)->count(),
        ];
        
        if (request()->wantsJson()) {
            return response()->json(['stats' => $stats]);
        }
        
        return view('shifts.index', compact('shifts', 'stats'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        Shift::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Shift created successfully.']);
        }

        return redirect()->route('shifts.index')->with('status', 'Shift created.');
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $shift->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Shift updated successfully.']);
        }

        return redirect()->route('shifts.index')->with('status', 'Shift updated.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Shift deleted successfully.']);
        }

        return back()->with('status', 'Shift deleted.');
    }
}
