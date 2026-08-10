<?php

namespace App\Http\Controllers;

use App\Models\UltrasoundService;
use Illuminate\Http\Request;

class UltrasoundServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $services = UltrasoundService::latest()->paginate(20);
        return view('ultrasound.services.index', compact('services'));
    }

    public function create()
    {
        return view('ultrasound.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:ultrasound_services',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $service = UltrasoundService::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Ultrasound service created.', 'service' => $service]);
        }

        return redirect()->route('ultrasound-services.index')->with('status', 'Ultrasound service created.');
    }

    public function edit(UltrasoundService $ultrasoundService)
    {
        if (request()->wantsJson()) {
            return response()->json(['service' => $ultrasoundService]);
        }
        return view('ultrasound.services.edit', compact('ultrasoundService'));
    }

    public function update(Request $request, UltrasoundService $ultrasoundService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:ultrasound_services,code,' . $ultrasoundService->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $ultrasoundService->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Ultrasound service updated.', 'service' => $ultrasoundService]);
        }

        return redirect()->route('ultrasound-services.index')->with('status', 'Ultrasound service updated.');
    }

    public function destroy(UltrasoundService $ultrasoundService)
    {
        $ultrasoundService->delete();
        return back()->with('status', 'Ultrasound service deleted.');
    }
}
