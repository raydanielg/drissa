<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(25);

        return view('activity_logs.index', compact('logs'));
    }
}
