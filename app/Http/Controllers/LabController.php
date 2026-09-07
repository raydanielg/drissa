<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\LabAttachment;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LabController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function queue()
    {
        $pendingOrders = LabOrder::with(['visit.patient', 'items.labTest', 'doctor'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $processingOrders = LabOrder::with(['visit.patient', 'items.labTest', 'items.results', 'labTech'])
            ->where('status', 'processing')
            ->latest()
            ->get();

        $completedOrders = LabOrder::with(['visit.patient', 'items.labTest', 'labTech', 'results', 'attachments'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->limit(20)
            ->get();

        $stats = [
            'pending' => $pendingOrders->count(),
            'processing' => $processingOrders->count(),
            'completed_today' => LabOrder::where('status', 'completed')->whereDate('completed_at', today())->count(),
            'total_tests' => LabOrderItem::whereHas('labOrder', fn ($q) => $q->whereIn('status', ['pending', 'processing']))->count(),
        ];

        return view('lab.queue', compact('pendingOrders', 'processingOrders', 'completedOrders', 'stats'));
    }

    public function startProcessing(LabOrder $order, VisitWorkflow $flow)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'This order is already being processed or completed.');
        }

        $order->update([
            'status' => 'processing',
            'processed_by' => auth()->id(),
        ]);
        $flow->transition($order->visit, VisitStatus::InLab);

        ActivityLog::log('lab_processing_started', $order->visit, "Started processing lab order #{$order->id} for visit {$order->visit->visit_number}");

        return back()->with('status', 'Processing started for Order #' . $order->id);
    }

    public function submitResults(Request $request, LabOrder $order, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'results' => 'nullable|array',
            'results.*.lab_order_item_id' => 'required_with:results|exists:lab_order_items,id',
            'results.*.parameter' => 'required_with:results|string',
            'results.*.value' => 'required_with:results|string',
            'results.*.unit' => 'nullable|string',
            'results.*.reference_range' => 'nullable|string',
            'results.*.flag' => 'required_with:results|in:normal,high,low,critical',
            'report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $hasResults = !empty($data['results']);
        $hasFile = $request->hasFile('report');

        if (!$hasResults && !$hasFile) {
            return back()->with('error', 'Please provide results or attach a file.');
        }

        DB::transaction(function () use ($order, $data, $request, $hasResults) {
            if ($hasResults) {
                foreach ($data['results'] as $item) {
                    LabResult::create([
                        'lab_order_item_id' => $item['lab_order_item_id'],
                        'parameter' => $item['parameter'],
                        'value' => $item['value'],
                        'unit' => $item['unit'] ?? null,
                        'reference_range' => $item['reference_range'] ?? null,
                        'flag' => $item['flag'],
                    ]);
                }
            }

            if ($request->hasFile('report')) {
                $file = $request->file('report');
                $path = $file->store('lab-reports', 'public');

                LabAttachment::create([
                    'lab_order_id' => $order->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }

            if ($hasResults) {
                $order->update([
                    'processed_by' => auth()->id(),
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
                $order->update(['processed_by' => auth()->id()]);
            }
        });

        if ($hasResults) {
            $flow->transition($order->visit, VisitStatus::LabCompleted);
            ActivityLog::log('lab_results_submitted', $order->visit, "Submitted lab results for visit {$order->visit->visit_number}");
            return back()->with('status', 'Lab results submitted successfully. Results sent back to doctor.');
        }

        ActivityLog::log('lab_attachment_uploaded', $order->visit, "Uploaded report attachment for order #{$order->id}");
        return back()->with('status', 'Report attached successfully.');
    }

    public function submitSingleResult(Request $request, LabOrder $order, LabOrderItem $item, VisitWorkflow $flow)
    {
        if ($item->lab_order_id !== $order->id) {
            return back()->with('error', 'This test item does not belong to this order.');
        }

        $data = $request->validate([
            'parameter' => 'required|string',
            'value' => 'required|string',
            'unit' => 'nullable|string',
            'reference_range' => 'nullable|string',
            'flag' => 'required|in:normal,high,low,critical',
        ]);

        LabResult::create([
            'lab_order_item_id' => $item->id,
            'parameter' => $data['parameter'],
            'value' => $data['value'],
            'unit' => $data['unit'] ?? null,
            'reference_range' => $data['reference_range'] ?? null,
            'flag' => $data['flag'],
        ]);

        ActivityLog::log('lab_result_entered', $order->visit, "Entered result for {$item->labTest?->name} on order #{$order->id}");

        $allItemsHaveResults = $order->items()->with('results')->get()->every(fn ($i) => $i->results->isNotEmpty());

        if ($allItemsHaveResults) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $flow->transition($order->visit, VisitStatus::LabCompleted);
            ActivityLog::log('lab_results_completed', $order->visit, "All lab results completed for visit {$order->visit->visit_number}");
            return back()->with('status', 'Result saved. All tests completed — order sent to doctor.');
        }

        return back()->with('status', 'Result saved for ' . ($item->labTest?->name ?? 'test') . '. Remaining tests still pending.');
    }

    public function showResults(LabOrder $order)
    {
        $order->load(['visit.patient', 'items.labTest', 'results.labOrderItem', 'attachments', 'doctor', 'labTech']);

        return view('lab.results', compact('order'));
    }

    public function history(Request $request)
    {
        $query = Patient::withCount(['labOrders as total_orders' => fn($q) => $q->where('lab_orders.status', 'completed')])
            ->having('total_orders', '>', 0)
            ->orderByDesc('total_orders');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mrn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(20);

        return view('lab.history', compact('patients'));
    }

    public function patientHistory(Patient $patient)
    {
        $orders = LabOrder::with(['visit', 'items.labTest', 'results.labOrderItem', 'attachments', 'doctor', 'labTech'])
            ->whereHas('visit', fn($q) => $q->where('patient_id', $patient->id))
            ->latest()
            ->get();

        $totalTests = $orders->flatMap->items->count();
        $completedOrders = $orders->where('status', 'completed');
        $abnormalResults = $completedOrders->flatMap->results->whereNotIn('flag', ['normal'])->count();

        return view('lab.patient-history', compact('patient', 'orders', 'totalTests', 'completedOrders', 'abnormalResults'));
    }
}
