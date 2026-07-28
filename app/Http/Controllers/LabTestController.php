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
        $test = LabTest::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Test type created.', 'test' => $test]);
        }

        return redirect()->route('lab-tests.index')->with('status', 'Test type created.');
    }

    public function edit(LabTest $labTest)
    {
        if (request()->wantsJson()) {
            return response()->json(['test' => $labTest]);
        }
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

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Test type updated.', 'test' => $labTest]);
        }

        return redirect()->route('lab-tests.index')->with('status', 'Test type updated.');
    }

    public function destroy(LabTest $labTest)
    {
        $labTest->delete();
        return back()->with('status', 'Test type deleted.');
    }

    public function sample()
    {
        $headers = ['name', 'code', 'unit', 'reference_range', 'price', 'is_active', 'description'];

        $rows = [
            ['Complete Blood Count', 'CBC', 'cells/uL', '4.0-11.0 x10^3/uL', '15000', '1', 'Full blood count analysis'],
            ['Blood Glucose (Fasting)', 'GLU-F', 'mmol/L', '3.9-6.1', '5000', '1', 'Fasting blood sugar test'],
            ['Blood Glucose (Random)', 'GLU-R', 'mmol/L', '3.9-11.1', '5000', '1', 'Random blood sugar test'],
            ['HbA1c', 'HBA1C', '%', '4.0-5.6', '25000', '1', 'Glycated hemoglobin test for diabetes monitoring'],
            ['Lipid Profile', 'LIPID', 'mmol/L', 'See report', '20000', '1', 'Total cholesterol, HDL, LDL, triglycerides'],
            ['Liver Function Test', 'LFT', 'U/L', 'See report', '25000', '1', 'ALT, AST, ALP, bilirubin, albumin'],
            ['Kidney Function Test', 'KFT', 'umol/L', 'See report', '25000', '1', 'Urea, creatinine, eGFR'],
            ['Thyroid Function Test', 'TFT', 'mIU/L', 'See report', '35000', '1', 'TSH, T3, T4'],
            ['Urinalysis', 'URINE', '-', 'See report', '5000', '1', 'Full urine analysis including microscopy'],
            ['Stool Analysis', 'STOOL', '-', 'See report', '5000', '1', 'Stool routine and microscopy'],
            ['Malaria Parasite (Blood Smear)', 'MPBS', '-', 'Negative', '3000', '1', 'Thick and thin blood smear for malaria'],
            ['Widal Test', 'WIDAL', '-', 'Negative', '5000', '1', 'Typhoid fever screening test'],
            ['Brucella Test', 'BRUCEL', '-', 'Negative', '8000', '1', 'Brucellosis screening test'],
            ['Rheumatoid Factor', 'RF', 'IU/mL', '<14', '10000', '1', 'Rheumatoid arthritis screening'],
            ['CRP (C-Reactive Protein)', 'CRP', 'mg/L', '<5', '12000', '1', 'Inflammation marker test'],
            ['ESR (Erythrocyte Sedimentation Rate)', 'ESR', 'mm/hr', '0-20', '5000', '1', 'Non-specific inflammation indicator'],
            ['Pregnancy Test (Urine)', 'PT-U', '-', 'Negative', '3000', '1', 'hCG urine pregnancy test'],
            ['Pregnancy Test (Blood)', 'PT-B', 'mIU/mL', '<5 (non-pregnant)', '8000', '1', 'Quantitative hCG blood test'],
            ['HIV Test (Rapid)', 'HIV-R', '-', 'Negative', '5000', '1', 'Rapid HIV screening test'],
            ['Hepatitis B Surface Antigen', 'HBsAg', '-', 'Negative', '10000', '1', 'Hepatitis B screening test'],
            ['Hepatitis C Antibody', 'HCV-Ab', '-', 'Negative', '12000', '1', 'Hepatitis C screening test'],
            ['Syphilis (VDRL)', 'VDRL', '-', 'Negative', '5000', '1', 'Syphilis screening test'],
            ['Blood Grouping (ABO + Rh)', 'BG', '-', 'See report', '3000', '1', 'ABO and RhD blood typing'],
            ['Cross Match', 'XM', '-', 'Compatible', '5000', '1', 'Blood compatibility testing'],
            ['Semen Analysis', 'SEMIN', '-', 'See report', '10000', '1', 'Fertility assessment test'],
            ['Vitamin D', 'VITD', 'ng/mL', '30-100', '30000', '1', '25-hydroxy vitamin D test'],
            ['Vitamin B12', 'VITB12', 'pg/mL', '200-900', '25000', '1', 'Vitamin B12 level test'],
            ['Iron Studies', 'IRON', 'umol/L', 'See report', '20000', '1', 'Serum iron, ferritin, TIBC'],
            ['Dengue NS1 Antigen', 'DENGUE', '-', 'Negative', '15000', '1', 'Dengue fever early detection'],
            ['COVID-19 PCR', 'COVID', '-', 'Negative', '50000', '1', 'SARS-CoV-2 PCR test'],
        ];

        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'lab-test-types-sample.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle);

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) {
                $skipped++;
                continue;
            }

            $data = array_combine($headers, $row);
            if ($data === false) {
                $skipped++;
                continue;
            }

            $name = trim($data['name'] ?? '');
            if (!$name) {
                $skipped++;
                continue;
            }

            $code = trim($data['code'] ?? '');
            if ($code && LabTest::where('code', $code)->exists()) {
                $skipped++;
                continue;
            }

            LabTest::create([
                'name' => $name,
                'code' => $code ?: null,
                'unit' => trim($data['unit'] ?? '') ?: null,
                'reference_range' => trim($data['reference_range'] ?? '') ?: null,
                'price' => (float) ($data['price'] ?? 0),
                'is_active' => filter_var($data['is_active'] ?? '1', FILTER_VALIDATE_BOOLEAN),
                'description' => trim($data['description'] ?? '') ?: null,
            ]);
            $imported++;
        }
        fclose($handle);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Imported {$imported} test types" . ($skipped > 0 ? ", skipped {$skipped} duplicates/invalid" : '') . '.',
            ]);
        }

        return back()->with('status', "Imported {$imported} test types, skipped {$skipped}.");
    }
}
