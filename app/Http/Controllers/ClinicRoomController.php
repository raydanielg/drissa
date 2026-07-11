<?php

namespace App\Http\Controllers;

use App\Models\ClinicRoom;
use App\Models\Department;
use Illuminate\Http\Request;

class ClinicRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rooms = ClinicRoom::with('department')->latest()->paginate(20);
        return view('clinic_rooms.index', compact('rooms'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('clinic_rooms.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:clinic_rooms',
            'department_id' => 'nullable|exists:departments,id',
            'type' => 'required|in:consultation,procedure,ward,emergency',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ]);

        ClinicRoom::create($data);

        return redirect()->route('clinic-rooms.index')->with('status', 'Room created.');
    }

    public function edit(ClinicRoom $clinicRoom)
    {
        $departments = Department::where('is_active', true)->get();
        return view('clinic_rooms.edit', compact('clinicRoom', 'departments'));
    }

    public function update(Request $request, ClinicRoom $clinicRoom)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:clinic_rooms,code,' . $clinicRoom->id,
            'department_id' => 'nullable|exists:departments,id',
            'type' => 'required|in:consultation,procedure,ward,emergency',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ]);

        $clinicRoom->update($data);

        return redirect()->route('clinic-rooms.index')->with('status', 'Room updated.');
    }

    public function destroy(ClinicRoom $clinicRoom)
    {
        $clinicRoom->delete();
        return back()->with('status', 'Room deleted.');
    }
}
