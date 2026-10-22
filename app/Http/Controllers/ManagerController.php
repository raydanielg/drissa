<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\ManagerReport;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'revenue_today' => Payment::whereDate('created_at', today())->sum('amount'),
            'revenue_this_week' => Payment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount'),
            'visits_today' => Visit::whereDate('registered_at', today())->count(),
            'new_patients_week' => Patient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'active_visits' => Visit::whereNotIn('status', [VisitStatus::Completed->value, VisitStatus::Cancelled->value])->count(),
            'completed_today' => Visit::where('status', VisitStatus::Completed->value)->whereDate('completed_at', today())->count(),
            'reports_total' => ManagerReport::when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))->count(),
            'reports_this_week' => ManagerReport::when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
                ->whereBetween('report_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        $recentReports = ManagerReport::with('user')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->latest('report_date')
            ->latest()
            ->limit(6)
            ->get();

        $todayReport = ManagerReport::where('user_id', $user->id)
            ->where('type', 'daily')
            ->whereDate('report_date', today())
            ->first();

        $weekReport = ManagerReport::where('user_id', $user->id)
            ->where('type', 'weekly')
            ->whereDate('report_date', now()->endOfWeek())
            ->first();

        return view('manager.dashboard', compact('stats', 'recentReports', 'todayReport', 'weekReport'));
    }

    public function reports(Request $request)
    {
        $user = auth()->user();

        $reports = ManagerReport::with('user')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('report_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('report_date', '<=', $request->to))
            ->latest('report_date')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('manager.reports.index', compact('reports'));
    }

    public function createReport(Request $request)
    {
        $type = $request->get('type', 'daily') === 'weekly' ? 'weekly' : 'daily';
        [$start, $end, $reportDate] = $this->resolvePeriod($request, $type);

        $suggestions = $this->periodSuggestions($start, $end);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $existing = ManagerReport::where('user_id', auth()->id())
            ->where('type', $type)
            ->whereDate('report_date', $reportDate)
            ->first();

        return view('manager.reports.create', compact('type', 'start', 'end', 'reportDate', 'suggestions', 'departments', 'existing'));
    }

    public function storeReport(Request $request)
    {
        $data = $this->validateReport($request);

        if ($data['type'] === 'weekly') {
            $data['report_date'] = $data['week_end'];
        }

        $exists = ManagerReport::where('user_id', auth()->id())
            ->where('type', $data['type'])
            ->whereDate('report_date', $data['report_date'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'A '.$data['type'].' report for this period already exists. Edit the existing one instead.');
        }

        $data['user_id'] = auth()->id();
        $data['department_reports'] = collect($request->input('department_reports', []))
            ->filter(fn ($row) => filled($row['name'] ?? null) || filled($row['report'] ?? null))
            ->values()
            ->all();

        $report = ManagerReport::create($data);

        ActivityLog::log('manager_report_created', $report, "Created {$report->type} manager report for {$report->periodLabel()}");

        return redirect()->route('manager.reports.show', $report)->with('status', ucfirst($report->type).' report saved successfully.');
    }

    public function showReport(ManagerReport $report)
    {
        $this->authorizeReport($report);

        return view('manager.reports.show', compact('report'));
    }

    public function editReport(ManagerReport $report)
    {
        $this->authorizeReport($report);

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $type = $report->type;
        $start = $report->week_start ?? $report->report_date;
        $end = $report->week_end ?? $report->report_date;
        $reportDate = $report->report_date;
        $suggestions = [];
        $existing = null;

        return view('manager.reports.edit', compact('report', 'departments', 'type', 'start', 'end', 'reportDate', 'suggestions', 'existing'));
    }

    public function updateReport(Request $request, ManagerReport $report)
    {
        $this->authorizeReport($report);

        $data = $this->validateReport($request, $report->type);
        $data['type'] = $report->type;
        $data['report_date'] = $report->report_date;
        $data['week_start'] = $report->week_start;
        $data['week_end'] = $report->week_end;
        $data['department_reports'] = collect($request->input('department_reports', []))
            ->filter(fn ($row) => filled($row['name'] ?? null) || filled($row['report'] ?? null))
            ->values()
            ->all();

        $report->update($data);

        ActivityLog::log('manager_report_updated', $report, "Updated {$report->type} manager report for {$report->periodLabel()}");

        return redirect()->route('manager.reports.show', $report)->with('status', 'Report updated successfully.');
    }

    public function destroyReport(ManagerReport $report)
    {
        $this->authorizeReport($report);

        ActivityLog::log('manager_report_deleted', $report, "Deleted {$report->type} manager report for {$report->periodLabel()}");
        $report->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Report deleted.']);
        }

        return redirect()->route('manager.reports.index')->with('status', 'Report deleted.');
    }

    public function suggestions(Request $request)
    {
        $type = $request->get('type', 'daily') === 'weekly' ? 'weekly' : 'daily';
        [$start, $end] = $this->resolvePeriod($request, $type);

        return response()->json($this->periodSuggestions($start, $end));
    }

    protected function resolvePeriod(Request $request, string $type): array
    {
        if ($type === 'weekly') {
            $anchor = $request->filled('date')
                ? Carbon::parse($request->date)
                : now();

            $start = $anchor->copy()->startOfWeek();
            $end = $anchor->copy()->endOfWeek();

            return [$start, $end, $end->copy()];
        }

        $day = $request->filled('date') ? Carbon::parse($request->date) : today();

        return [$day->copy()->startOfDay(), $day->copy()->endOfDay(), $day->copy()];
    }

    protected function periodSuggestions(Carbon $start, Carbon $end): array
    {
        $revenue = Payment::whereBetween('created_at', [$start, $end])->sum('amount');

        $newClients = Patient::whereBetween('created_at', [$start, $end])->count();

        $returningClients = Visit::whereBetween('registered_at', [$start, $end])
            ->whereHas('patient', fn ($q) => $q->where('created_at', '<', $start))
            ->distinct()
            ->count('patient_id');

        $productSales = DB::table('dispenses')
            ->join('medications', 'dispenses.medication_id', '=', 'medications.id')
            ->whereBetween('dispenses.created_at', [$start, $end])
            ->sum(DB::raw('dispenses.quantity * medications.unit_price'));

        return [
            'revenue_overall' => round((float) $revenue, 2),
            'product_sales_costs' => round((float) $productSales, 2),
            'new_clients' => $newClients,
            'returning_clients' => $returningClients,
        ];
    }

    protected function validateReport(Request $request, ?string $fixedType = null): array
    {
        return $request->validate([
            'type' => ($fixedType ? 'sometimes' : 'required').'|in:daily,weekly',
            'report_date' => 'required|date',
            'week_start' => 'nullable|required_if:type,weekly|date',
            'week_end' => 'nullable|required_if:type,weekly|date|after_or_equal:week_start',
            'revenue_overall' => 'nullable|numeric|min:0',
            'supplement_sales_ppv' => 'nullable|numeric|min:0',
            'product_sales_costs' => 'nullable|numeric|min:0',
            'new_clients' => 'nullable|integer|min:0',
            'returning_clients' => 'nullable|integer|min:0',
            'lab_tools_expenses' => 'nullable|numeric|min:0',
            'office_expenses' => 'nullable|numeric|min:0',
            'department_reports' => 'nullable|array',
            'department_reports.*.name' => 'nullable|string|max:255',
            'department_reports.*.report' => 'nullable|string|max:2000',
            'staff_performance' => 'nullable|string|max:5000',
            'challenges' => 'nullable|string|max:5000',
            'action_plan' => 'nullable|string|max:5000',
            'meeting_reports' => 'nullable|string|max:5000',
            'manager_signature' => 'nullable|string|max:255',
        ]);
    }

    protected function authorizeReport(ManagerReport $report): void
    {
        abort_unless(auth()->user()->isAdmin() || $report->user_id === auth()->id(), 403);
    }
}
