<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Patient $patient)
    {
        $documents = $patient->documents()->with('uploader')->paginate(20);
        return view('patients.documents', compact('patient', 'documents'));
    }

    public function store(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:lab,radiology,prescription,identification,insurance,other',
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('patient_documents/' . $patient->id, 'public');

        $document = PatientDocument::create([
            'patient_id' => $patient->id,
            'uploaded_by' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Document uploaded.', 'document' => $document]);
        }

        return back()->with('status', 'Document uploaded successfully.');
    }

    public function download(PatientDocument $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->title);
    }

    public function destroy(PatientDocument $document)
    {
        $document->delete();
        return back()->with('status', 'Document deleted.');
    }
}
