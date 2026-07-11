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
        return view('lab_equipment.index', compact('equipment'));
    }

    public function create()
    {
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

        LabEquipment::create($data);

        return redirect()->route('lab-equipment.index')->with('status', 'Equipment added.');
    }

    public function edit(LabEquipment $labEquipment)
    {
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

        return redirect()->route('lab-equipment.index')->with('status', 'Equipment updated.');
    }

    public function destroy(LabEquipment $labEquipment)
    {
        $labEquipment->delete();
        return back()->with('status', 'Equipment removed.');
    }
}
