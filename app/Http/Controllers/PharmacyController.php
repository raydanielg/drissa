<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Dispense;
use App\Models\Medication;
use App\Models\Prescription;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $stockValue = Medication::sum(DB::raw('stock_quantity * purchase_price'));
        $retailValue = Medication::sum(DB::raw('stock_quantity * unit_price'));
        $potentialProfit = $retailValue - $stockValue;

        $totalRevenue = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->sum(DB::raw('dispenses.quantity * medications.unit_price'));
        $totalCost = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->sum(DB::raw('dispenses.quantity * medications.purchase_price'));
        $totalProfit = $totalRevenue - $totalCost;

        $todayRevenue = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->whereDate('dispenses.created_at', today())
            ->sum(DB::raw('dispenses.quantity * medications.unit_price'));
        $todayCost = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->whereDate('dispenses.created_at', today())
            ->sum(DB::raw('dispenses.quantity * medications.purchase_price'));
        $todayProfit = $todayRevenue - $todayCost;

        $stats = [
            'total_medicines' => Medication::count(),
            'low_stock' => Medication::whereColumn('stock_quantity', '<=', 'reorder_level')->count(),
            'out_of_stock' => Medication::where('stock_quantity', 0)->count(),
            'pending_prescriptions' => Prescription::where('status', 'pending')->count(),
            'dispensed_today' => Dispense::whereDate('created_at', today())->count(),
            'expiring_soon' => Medication::where('expiry_date', '<', now()->addDays(30))->where('expiry_date', '>', now())->count(),
            'stock_value' => $stockValue,
            'retail_value' => $retailValue,
            'potential_profit' => $potentialProfit,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'today_revenue' => $todayRevenue,
            'today_profit' => $todayProfit,
        ];

        $recentPrescriptions = Prescription::with(['visit.patient', 'doctor'])
            ->latest()
            ->limit(8)
            ->get();

        $lowStockMeds = Medication::whereColumn('stock_quantity', '<=', 'reorder_level')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        $topMeds = Dispense::select('medication_id', DB::raw('sum(quantity) as total_qty'))
            ->with('medication')
            ->groupBy('medication_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('pharmacy.dashboard', compact('stats', 'recentPrescriptions', 'lowStockMeds', 'topMeds'));
    }

    public function queue()
    {
        $prescriptions = Prescription::with(['visit.patient', 'items.medication'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('pharmacy.queue', compact('prescriptions'));
    }

    public function inventory()
    {
        $medications = Medication::orderBy('name')->paginate(20);

        $lowStock = Medication::whereColumn('stock_quantity', '<=', 'reorder_level')->count();
        $outOfStock = Medication::where('stock_quantity', 0)->count();
        $stockValue = Medication::sum(DB::raw('stock_quantity * purchase_price'));
        $retailValue = Medication::sum(DB::raw('stock_quantity * unit_price'));
        $potentialProfit = $retailValue - $stockValue;

        return view('pharmacy.inventory', compact('medications', 'lowStock', 'outOfStock', 'stockValue', 'retailValue', 'potentialProfit'));
    }

    public function storeMedication(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:100',
            'batch_no' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Medication::create($data);

        ActivityLog::log('medication_created', null, "Added medication: {$data['name']}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Medication added successfully.']);
        }

        return back()->with('status', 'Medication added successfully.');
    }

    public function updateMedication(Request $request, Medication $medication)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'form' => 'nullable|string|max:100',
            'batch_no' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $medication->update($data);

        ActivityLog::log('medication_updated', null, "Updated medication: {$data['name']}");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Medication updated successfully.']);
        }

        return back()->with('status', 'Medication updated successfully.');
    }

    public function deleteMedication(Medication $medication)
    {
        ActivityLog::log('medication_deleted', null, "Deleted medication: {$medication->name}");
        $medication->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Medication deleted.']);
        }

        return back()->with('status', 'Medication deleted.');
    }

    public function editMedication(Medication $medication)
    {
        if (request()->wantsJson()) {
            return response()->json(['medication' => $medication]);
        }

        return back();
    }

    public function prescriptionHistory()
    {
        $prescriptions = Prescription::with(['visit.patient', 'doctor', 'items.medication'])
            ->whereIn('status', ['dispensed', 'cancelled'])
            ->latest()
            ->paginate(20);

        return view('pharmacy.history', compact('prescriptions'));
    }

    public function reports()
    {
        $totalDispensed = Dispense::count();
        $dispensedToday = Dispense::whereDate('created_at', today())->count();

        $totalRevenue = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->sum(DB::raw('dispenses.quantity * medications.unit_price'));
        $totalCost = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->sum(DB::raw('dispenses.quantity * medications.purchase_price'));
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $topMedicines = Dispense::select('medication_id', DB::raw('sum(quantity) as total_qty'), DB::raw('sum(quantity * medications.unit_price) as revenue'), DB::raw('sum(quantity * medications.purchase_price) as cost'))
            ->join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->with('medication')
            ->groupBy('medication_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $monthlyDispenses = collect();
        $monthlyRevenue = collect();
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Dispense::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $rev = Dispense::join('medications', 'dispenses.medication_id', '=', 'medications.id')
                ->whereMonth('dispenses.created_at', $date->month)
                ->whereYear('dispenses.created_at', $date->year)
                ->sum(DB::raw('dispenses.quantity * medications.unit_price'));
            $monthlyDispenses->push($count);
            $monthlyRevenue->push($rev);
            $monthLabels[] = $date->format('M Y');
        }

        return view('pharmacy.reports', compact(
            'totalDispensed', 'dispensedToday', 'totalRevenue', 'totalCost', 'totalProfit', 'profitMargin',
            'topMedicines', 'monthlyDispenses', 'monthlyRevenue', 'monthLabels'
        ));
    }

    public function dispense(Request $request, Prescription $prescription, VisitWorkflow $flow)
    {
        DB::transaction(function () use ($prescription) {
            foreach ($prescription->items as $item) {
                $item->medication->decrement('stock_quantity', $item->quantity);

                Dispense::create([
                    'prescription_id' => $prescription->id,
                    'medication_id' => $item->medication_id,
                    'quantity' => $item->quantity,
                    'dispensed_by' => auth()->id(),
                ]);
            }

            $prescription->update(['status' => 'dispensed']);
        });

        $flow->transition($prescription->visit, VisitStatus::WaitingForPayment);

        ActivityLog::log('drugs_dispensed', $prescription->visit, "Dispensed drugs for visit {$prescription->visit->visit_number}");

        return back()->with('status', 'Drugs dispensed.');
    }
}
