<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\UltrasoundAttachment;
use App\Models\UltrasoundOrder;
use App\Models\UltrasoundOrderItem;
use App\Models\UltrasoundService;
use App\Models\Visit;
use App\Services\VisitWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UltrasoundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $today = today();

        $pendingCount = UltrasoundOrder::where('status', 'pending')->count();
        $processingCount = UltrasoundOrder::where('status', 'processing')->count();
        $completedToday = UltrasoundOrder::where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->count();
        $totalToday = UltrasoundOrder::whereDate('created_at', $today)->count();

        $recentOrders = UltrasoundOrder::with(['patient', 'visit', 'items.ultrasoundService', 'doctor', 'ultrasoundTech'])
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'pending' => $pendingCount,
            'processing' => $processingCount,
            'completed_today' => $completedToday,
            'total_today' => $totalToday,
        ];

        return view('ultrasound.dashboard', compact('stats', 'recentOrders'));
    }

    public function queue()
    {
        $pendingOrders = UltrasoundOrder::with(['patient', 'visit', 'items.ultrasoundService', 'doctor'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $processingOrders = UltrasoundOrder::with(['patient', 'visit', 'items.ultrasoundService', 'ultrasoundTech'])
            ->where('status', 'processing')
            ->latest()
            ->get();

        $completedOrders = UltrasoundOrder::with(['patient', 'visit', 'items.ultrasoundService', 'ultrasoundTech', 'attachments'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->limit(20)
            ->get();

        $stats = [
            'pending' => $pendingOrders->count(),
            'processing' => $processingOrders->count(),
            'completed_today' => UltrasoundOrder::where('status', 'completed')->whereDate('completed_at', today())->count(),
            'total_services' => UltrasoundOrderItem::whereHas('ultrasoundOrder', fn ($q) => $q->whereIn('status', ['pending', 'processing']))->count(),
        ];

        return view('ultrasound.queue', compact('pendingOrders', 'processingOrders', 'completedOrders', 'stats'));
    }

    public function startProcessing(UltrasoundOrder $order, VisitWorkflow $flow)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'This order is already being processed or completed.');
        }

        $order->update([
            'status' => 'processing',
            'processed_by' => auth()->id(),
        ]);

        if ($order->visit) {
            $flow->transition($order->visit, VisitStatus::InUltrasound);
        }

        ActivityLog::log('ultrasound_processing_started', $order->visit ?? $order->patient, "Started processing ultrasound order #{$order->id}");

        return back()->with('status', 'Processing started for Order #' . $order->id);
    }

    public function submitResults(Request $request, UltrasoundOrder $order, VisitWorkflow $flow)
    {
        $data = $request->validate([
            'findings' => 'nullable|string',
            'impression' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dicom|max:10240',
        ]);

        DB::transaction(function () use ($order, $data, $request, $flow) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
                'findings' => $data['findings'] ?? null,
                'impression' => $data['impression'] ?? null,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ultrasound-reports', 'public');

                    UltrasoundAttachment::create([
                        'ultrasound_order_id' => $order->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            if ($order->visit) {
                $flow->transition($order->visit, VisitStatus::UltrasoundCompleted);
            }
        });

        ActivityLog::log('ultrasound_results_submitted', $order->visit ?? $order->patient, "Submitted ultrasound results for order #{$order->id}");

        return back()->with('status', 'Ultrasound results submitted successfully.');
    }

    public function showResults(UltrasoundOrder $order)
    {
        $order->load(['patient', 'visit', 'items.ultrasoundService', 'attachments', 'doctor', 'ultrasoundTech']);
        return view('ultrasound.results', compact('order'));
    }
}
