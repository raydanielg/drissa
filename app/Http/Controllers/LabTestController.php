<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tests = LabTest::latest()->paginate(20);
        return view('lab_tests.index', compact('tests'));
    }

    public function create()
    {
        return view('lab_tests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:lab_tests',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        LabTest::create($data);

        return redirect()->route('lab-tests.index')->with('status', 'Test type created.');
    }

    public function edit(LabTest $labTest)
    {
        return view('lab_tests.edit', compact('labTest'));
    }

    public function update(Request $request, LabTest $labTest)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:lab_tests,code,' . $labTest->id,
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'reference_range' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $labTest->update($data);

        return redirect()->route('lab-tests.index')->with('status', 'Test type updated.');
    }

    public function destroy(LabTest $labTest)
    {
        $labTest->delete();
        return back()->with('status', 'Test type deleted.');
    }
}
