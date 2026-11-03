<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manager Report - {{ $report->periodLabel() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.45; }
        .header { background: #14532d; color: #fff; padding: 18px 22px; }
        .header .brand { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header .sub { font-size: 10px; color: #fde68a; margin-top: 2px; }
        .title-bar { background: #f59e0b; color: #7c2d12; padding: 8px 22px; font-size: 12px; font-weight: bold; }
        .content { padding: 18px 22px; }
        .meta { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .meta td { padding: 3px 0; font-size: 10.5px; color: #4b5563; }
        .meta td.lbl { width: 90px; color: #9ca3af; text-transform: uppercase; font-size: 9px; letter-spacing: .05em; }
        h3 { font-size: 10px; text-transform: uppercase; letter-spacing: .08em; color: #14532d; border-bottom: 2px solid #14532d; padding-bottom: 3px; margin: 14px 0 8px; }
        table.metrics { width: 100%; border-collapse: collapse; }
        table.metrics th { background: #14532d; color: #fff; text-align: left; padding: 6px 8px; font-size: 9.5px; text-transform: uppercase; letter-spacing: .05em; }
        table.metrics td { border: 1px solid #d1d5db; padding: 6px 8px; font-size: 11px; }
        table.metrics tr:nth-child(even) td { background: #f0fdf4; }
        .metric-label { color: #374151; }
        .metric-value { font-weight: bold; text-align: right; white-space: nowrap; }
        .text-block { border: 1px solid #d1d5db; border-left: 3px solid #15803d; background: #f9fafb; padding: 8px 10px; margin-bottom: 6px; white-space: pre-wrap; }
        .dept { border: 1px solid #d1d5db; margin-bottom: 6px; }
        .dept .d-name { background: #e7f6ee; padding: 5px 8px; font-weight: bold; color: #14532d; font-size: 10.5px; }
        .dept .d-body { padding: 6px 8px; white-space: pre-wrap; color: #374151; }
        .sig { margin-top: 26px; width: 100%; }
        .sig td { width: 45%; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #9ca3af; padding-top: 4px; font-size: 9.5px; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; }
        .sig-name { font-weight: bold; font-size: 11px; margin-bottom: 16px; color: #14532d; }
        .footer { text-align: center; font-size: 8.5px; color: #9ca3af; margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%"><tr>
            <td><div class="brand">{{ config('app.name', 'Uzazi Clinic') }}</div>
                <div class="sub">Clinic Manager Report</div></td>
            <td style="text-align:right"><div style="font-size:10px;color:#d1fae5;">Generated {{ now()->format('d M Y H:i') }}</div>
                <div style="font-size:10px;color:#fde68a;font-weight:bold;">{{ strtoupper($report->type) }} REPORT</div></td>
        </tr></table>
    </div>
    <div class="title-bar">Report Period: {{ $report->periodLabel() }}</div>

    <div class="content">
        <table class="meta">
            <tr>
                <td class="lbl">Manager</td><td>{{ $report->user->name ?? '-' }}</td>
                <td class="lbl">Type</td><td>{{ ucfirst($report->type) }}</td>
            </tr>
            @if($report->type === 'weekly')
            <tr>
                <td class="lbl">Week</td><td>{{ $report->periodLabel() }}</td>
                <td class="lbl">Submitted</td><td>{{ $report->created_at?->format('d M Y H:i') }}</td>
            </tr>
            @else
            <tr>
                <td class="lbl">Date</td><td>{{ $report->report_date?->format('d M Y') }}</td>
                <td class="lbl">Submitted</td><td>{{ $report->created_at?->format('d M Y H:i') }}</td>
            </tr>
            @endif
        </table>

        <h3>Financials &amp; Attendance</h3>
        <table class="metrics">
            <tr><th>Metric</th><th style="text-align:right">Value</th></tr>
            <tr><td class="metric-label">Revenue Overall</td><td class="metric-value">{{ number_format($report->revenue_overall ?? 0) }} TSh</td></tr>
            <tr><td class="metric-label">Supplement Sales PPV</td><td class="metric-value">{{ number_format($report->supplement_sales_ppv ?? 0) }} TSh</td></tr>
            <tr><td class="metric-label">Product Sales [Costs]</td><td class="metric-value">{{ number_format($report->product_sales_costs ?? 0) }} TSh</td></tr>
            <tr><td class="metric-label">New Clients</td><td class="metric-value">{{ number_format($report->new_clients ?? 0) }}</td></tr>
            <tr><td class="metric-label">Returning Clients</td><td class="metric-value">{{ number_format($report->returning_clients ?? 0) }}</td></tr>
            <tr><td class="metric-label">Lab Tools Expenses</td><td class="metric-value">{{ number_format($report->lab_tools_expenses ?? 0) }} TSh</td></tr>
            <tr><td class="metric-label">Office Expenses [Matumizi]</td><td class="metric-value">{{ number_format($report->office_expenses ?? 0) }} TSh</td></tr>
        </table>

        @php $depts = $report->department_reports; if (is_string($depts)) { $depts = json_decode($depts, true); } @endphp
        @if(!empty($depts) && is_array($depts))
        <h3>Report From Every Department</h3>
        @foreach($depts as $d)
            @if(is_array($d))
            <div class="dept">
                <div class="d-name">{{ $d['department'] ?? $d['name'] ?? 'Department' }}</div>
                <div class="d-body">{{ $d['report'] ?? $d['notes'] ?? $d['summary'] ?? '' }}</div>
            </div>
            @endif
        @endforeach
        @endif

        @if(filled($report->staff_performance))
        <h3>Staff Performance</h3>
        <div class="text-block">{{ $report->staff_performance }}</div>
        @endif

        @if(filled($report->challenges))
        <h3>Challenges</h3>
        <div class="text-block">{{ $report->challenges }}</div>
        @endif

        @if(filled($report->action_plan))
        <h3>Action Plan</h3>
        <div class="text-block">{{ $report->action_plan }}</div>
        @endif

        @if(filled($report->meeting_reports))
        <h3>Meeting Reports &amp; Comments</h3>
        <div class="text-block">{{ $report->meeting_reports }}</div>
        @endif

        <table class="sig"><tr>
            <td><div class="sig-name">{{ $report->manager_signature ?: ($report->user->name ?? '') }}</div>
                <div class="sig-line">Manager Signature</div></td>
            <td></td>
            <td><div class="sig-name">&nbsp;</div>
                <div class="sig-line">Date</div></td>
        </tr></table>

        <div class="footer">{{ config('app.name', 'Uzazi Clinic') }} &bull; Clinic Manager Report &bull; Confidential</div>
    </div>
</body>
</html>
