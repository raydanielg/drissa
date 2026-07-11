<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\LabAttachment;
use App\Models\LabOrder;
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
        $orders = LabOrder::with(['visit.patient', 'items.labTest'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('lab.queue', compact('orders'));
    }

    public function startProcessing(LabOrder $order, VisitWorkflow $flow)
    {
        $order->update(['status' => 'processing']);
        $flow->transition($order->visit, VisitStatus::InLab);

        return back()->with('status', 'Processing started.');
    }

    public function submitResults(Request $request, LabOrder $order, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'results' => 'required|array',
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
                    'lab_order_item_id' => $order->items()->first()->id,
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

        return back()->with('status', 'Results submitted.');
    }
}
