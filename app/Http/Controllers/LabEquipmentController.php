<?php

namespace App\Http\Controllers;

use App\Models\LabEquipment;
use Illuminate\Http\Request;

class LabEquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $equipment = LabEquipment::latest()->paginate(20);
        $stats = [
            'total' => LabEquipment::count(),
            'active' => LabEquipment::where('status', 'active')->count(),
            'maintenance' => LabEquipment::where('status', 'maintenance')->count(),
            'retired' => LabEquipment::where('status', 'retired')->count(),
        ];
        return view('lab_equipment.index', compact('equipment', 'stats'));
    }

    public function create()
    {
        if (request()->wantsJson()) {
            return response()->json(['statuses' => ['active', 'maintenance', 'retired']]);
        }
        return view('lab_equipment.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:lab_equipment',
            'manufacturer' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'status' => 'required|in:active,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $item = LabEquipment::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Equipment added.', 'item' => $item]);
        }

        return redirect()->route('lab-equipment.index')->with('status', 'Equipment added.');
    }

    public function edit(LabEquipment $labEquipment)
    {
        if (request()->wantsJson()) {
            return response()->json(['item' => $labEquipment, 'statuses' => ['active', 'maintenance', 'retired']]);
        }
        return view('lab_equipment.edit', compact('labEquipment'));
    }

    public function update(Request $request, LabEquipment $labEquipment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:lab_equipment,serial_number,' . $labEquipment->id,
            'manufacturer' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'last_service_date' => 'nullable|date',
            'next_service_date' => 'nullable|date',
            'status' => 'required|in:active,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $labEquipment->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Equipment updated.', 'item' => $labEquipment]);
        }

        return redirect()->route('lab-equipment.index')->with('status', 'Equipment updated.');
    }

    public function destroy(LabEquipment $labEquipment)
    {
        $labEquipment->delete();
        return back()->with('status', 'Equipment removed.');
    }

    public function updateStatus(Request $request, LabEquipment $labEquipment)
    {
        $data = $request->validate([
            'status' => 'required|in:active,maintenance,retired',
        ]);

        $labEquipment->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status updated.', 'item' => $labEquipment]);
        }

        return back()->with('status', 'Status updated.');
    }
}
