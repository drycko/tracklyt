<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->report_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            padding: 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .company-logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }
        .document-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .report-number {
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .status-draft { background: #6c757d; color: white; }
        .status-in-progress { background: #ffc107; color: #333; }
        .status-completed { background: #198754; color: white; }
        .status-sent { background: #0dcaf0; color: white; }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
            background: #f8f9fa;
        }
        .info-left {
            border-right: 10px solid white;
        }
        .info-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-content {
            font-size: 11px;
            line-height: 1.6;
        }
        .info-content strong {
            display: block;
            color: #333;
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        
        .tasks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tasks-table thead {
            background: #667eea;
            color: white;
        }
        .tasks-table th {
            padding: 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #5568d3;
        }
        .tasks-table td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            font-size: 11px;
            vertical-align: top;
        }
        .tasks-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .tasks-table .text-center {
            text-align: center;
        }
        .task-number {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }
        .task-name {
            width: 30%;
        }
        .task-comment {
            width: 65%;
        }
        .task-completed {
            color: #198754;
            font-weight: bold;
        }
        .task-meta {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .task-screenshot {
            margin-top: 10px;
        }
        .task-screenshot img {
            max-width: 100%;
            height: auto;
            margin-bottom: 5px;
            border: 1px solid #e0e0e0;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fffbeb;
            border-left: 3px solid #fbbf24;
        }
        .notes-title {
            font-size: 11px;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .notes-content {
            font-size: 10px;
            color: #78350f;
            line-height: 1.6;
        }
        
        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border-left: 3px solid #3b82f6;
        }
        .summary-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .summary-content {
            font-size: 10px;
            color: #1e3a8a;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @if(auth()->user()->tenant && auth()->user()->tenant->company_logo)
                <img src="{{ public_path('storage/' . auth()->user()->tenant->company_logo) }}" alt="Logo" class="company-logo">
            @else
                <img src="{{ public_path('assets/images/tracklyt-logo-drk-wide.png') }}" alt="Tracklyt Logo" class="company-logo">
            @endif
            <div class="company-name">{{ auth()->user()->tenant->name ?? 'Tracklyt' }}</div>
            <div class="company-details">
                @if(auth()->user()->tenant)
                    {{ auth()->user()->tenant->email ?? '' }}<br>
                    {{ auth()->user()->tenant->phone ?? '' }}
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="document-title">{{ strtoupper($report->reportType->name) }}</div>
            <div class="report-number">{{ $report->report_number }}</div>
            @if($report->status === 'draft')
            <span class="status-badge status-draft">Draft</span>
            @elseif($report->status === 'in_progress')
            <span class="status-badge status-in-progress">In Progress</span>
            @elseif($report->status === 'completed')
            <span class="status-badge status-completed">Completed</span>
            @elseif($report->status === 'sent')
            <span class="status-badge status-sent">Sent</span>
            @endif
        </div>
    </div>

    <!-- Project & Report Info -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-title">Project Information</div>
            <div class="info-content">
                <strong>{{ $report->project->name }}</strong>
                @if($report->project->client)
                    Client: {{ $report->project->client->name }}<br>
                @endif
                @if($report->project->website)
                    Website: {{ $report->project->website }}<br>
                @endif
            </div>
        </div>
        <div class="info-right">
            <div class="info-title">Report Information</div>
            <div class="info-content">
                <strong>Date:</strong> {{ $report->scheduled_date ? $report->scheduled_date->format('M d, Y') : $report->created_at->format('M d, Y') }}<br>
                <strong>Assigned To:</strong> {{ $report->assignedTo->name ?? 'Unassigned' }}<br>
                @if($report->started_at)
                <strong>Started:</strong> {{ $report->started_at->format('M d, Y H:i') }}<br>
                @endif
                @if($report->completed_at)
                <strong>Completed:</strong> {{ $report->completed_at->format('M d, Y H:i') }}<br>
                @endif
                <strong>Created By:</strong> {{ $report->createdBy->name }}
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="section-title">TASK LIST</div>
    <table class="tasks-table">
        <thead>
            <tr>
                <th class="task-number">#</th>
                <th class="task-name">TASK</th>
                <th class="task-comment">COMMENT / NOTES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->tasks as $index => $task)
            <tr>
                <td class="task-number">{{ $index + 1 }}</td>
                <td>
                    {{ $task->task_name }}
                    @if($task->task_description)
                        <div class="task-meta">{{ $task->task_description }}</div>
                    @endif
                    @if($task->estimated_time_minutes || $task->time_spent_minutes)
                        <div class="task-meta">
                            @if($task->estimated_time_minutes)
                                Est: {{ $task->estimated_time_minutes }}m
                            @endif
                            @if($task->time_spent_minutes)
                                @if($task->estimated_time_minutes) | @endif
                                <strong>Actual: {{ $task->time_spent_minutes }}m</strong>
                            @endif
                        </div>
                    @endif
                    @if($task->is_completed)
                        <div class="task-completed">✓ Completed</div>
                    @endif
                </td>
                <td>
                    {{ $task->comments ?? '' }}
                    
                    @if($task->screenshots && count($task->screenshots) > 0)
                        <div class="task-screenshot">
                            @foreach($task->screenshots as $screenshot)
                                @php
                                    $imagePath = storage_path('app/public/' . $screenshot);
                                    if (file_exists($imagePath)) {
                                        $imageData = base64_encode(file_get_contents($imagePath));
                                        $imageExtension = pathinfo($screenshot, PATHINFO_EXTENSION);
                                        $mimeType = $imageExtension === 'png' ? 'image/png' : 'image/jpeg';
                                        echo '<img src="data:' . $mimeType . ';base64,' . $imageData . '" style="max-width: 100%;"><br>';
                                    }
                                @endphp
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">Report Summary</div>
        <div class="summary-content">
            <strong>Total Tasks:</strong> {{ $report->tasks->count() }}<br>
            <strong>Completed Tasks:</strong> {{ $report->tasks->where('is_completed', true)->count() }} ({{ $report->completion_percentage }}%)<br>
            <strong>Total Time Spent:</strong> {{ $report->tasks->sum('time_spent_minutes') }} minutes ({{ number_format($report->tasks->sum('time_spent_minutes') / 60, 1) }} hours)<br>
            @if($report->tasks->sum('estimated_time_minutes') > 0)
            <strong>Estimated Time:</strong> {{ $report->tasks->sum('estimated_time_minutes') }} minutes ({{ number_format($report->tasks->sum('estimated_time_minutes') / 60, 1) }} hours)
            @endif
        </div>
    </div>

    <!-- Report Notes -->
    @if($report->notes)
    <div class="notes-section">
        <div class="notes-title">Additional Notes</div>
        <div class="notes-content">
            {{ $report->notes }}
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>{{ $report->reportType->footer_text ?? 'MAINTENANCE REPORT' }}</p>
        <p style="margin-top: 5px;">Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
    </div>
</body>
</html>
