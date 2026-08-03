<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\LabAttachment;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
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

        $processingOrders = LabOrder::with(['visit.patient', 'items.labTest', 'labTech'])
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
            'results' => 'required|array',
            'results.*.lab_order_item_id' => 'required|exists:lab_order_items,id',
            'results.*.parameter' => 'required|string',
            'results.*.value' => 'required|string',
            'results.*.unit' => 'nullable|string',
            'results.*.reference_range' => 'nullable|string',
            'results.*.flag' => 'required|in:normal,high,low,critical',
            'report' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($order, $data, $request) {
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

            $order->update([
                'processed_by' => auth()->id(),
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        $flow->transition($order->visit, VisitStatus::LabCompleted);

        ActivityLog::log('lab_results_submitted', $order->visit, "Submitted lab results for visit {$order->visit->visit_number}");

        return back()->with('status', 'Lab results submitted successfully. Results sent back to doctor.');
    }

    public function showResults(LabOrder $order)
    {
        $order->load(['visit.patient', 'items.labTest', 'results.labOrderItem', 'attachments', 'doctor', 'labTech']);

        return view('lab.results', compact('order'));
    }
}
