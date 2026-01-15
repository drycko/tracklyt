<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
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
        .quote-number {
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
        .status-sent { background: #0dcaf0; color: white; }
        .status-approved { background: #198754; color: white; }
        .status-rejected { background: #dc3545; color: white; }
        .status-expired { background: #ffc107; color: #333; }
        
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
        
        .quote-details {
            margin-bottom: 25px;
        }
        .quote-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .quote-description {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 12px;
            background: #f8f9fa;
            border-left: 3px solid #667eea;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead {
            background: #667eea;
            color: white;
        }
        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #5568d3;
        }
        .items-table td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .category-cell {
            font-weight: 600;
            color: #667eea;
        }
        
        .totals-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .totals-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .totals-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
        }
        .totals-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .totals-label {
            display: table-cell;
            text-align: right;
            padding-right: 15px;
            font-size: 11px;
            color: #666;
        }
        .totals-value {
            display: table-cell;
            text-align: right;
            font-size: 11px;
            font-weight: 600;
            width: 120px;
        }
        .totals-row.total {
            border-top: 2px solid #667eea;
            border-bottom: 3px double #667eea;
            padding: 12px 0;
            margin-top: 5px;
        }
        .totals-row.total .totals-label {
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }
        .totals-row.total .totals-value {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
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
            <div class="document-title">QUOTATION</div>
            <div class="quote-number">{{ $quote->quote_number }}</div>
            @if($quote->status === 'draft')
            <span class="status-badge status-draft">Draft</span>
            @elseif($quote->status === 'sent')
            <span class="status-badge status-sent">Sent</span>
            @elseif($quote->status === 'approved')
            <span class="status-badge status-approved">Approved</span>
            @elseif($quote->status === 'rejected')
            <span class="status-badge status-rejected">Rejected</span>
            @else
            <span class="status-badge status-expired">Expired</span>
            @endif
        </div>
    </div>

    <!-- Client & Quote Info -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-title">Bill To</div>
            <div class="info-content">
                <strong>{{ $quote->client->name }}</strong>
                {{ $quote->client->email }}<br>
                @if($quote->client->phone){{ $quote->client->phone }}<br>@endif
                @if($quote->client->company){{ $quote->client->company }}<br>@endif
                @if($quote->client->address){{ $quote->client->address }}@endif
            </div>
        </div>
        <div class="info-right">
            <div class="info-title">Quote Information</div>
            <div class="info-content">
                <strong>Date:</strong> {{ $quote->created_at->format('M d, Y') }}<br>
                <strong>Valid Until:</strong> {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}<br>
                <strong>Prepared By:</strong> {{ $quote->creator->name }}<br>
                <strong>Email:</strong> {{ $quote->creator->email }}
            </div>
        </div>
    </div>

    <!-- Quote Title & Description -->
    @if($quote->title || $quote->description)
    <div class="quote-details">
        @if($quote->title)
        <div class="quote-title">{{ $quote->title }}</div>
        @endif
        
        @if($quote->description)
        <div class="quote-description">{{ $quote->description }}</div>
        @endif
    </div>
    @endif

    <!-- Line Items Table -->
    @if($quote->lineItems && $quote->lineItems->count() > 0)
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Category</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 10%;" class="text-center">Hours</th>
                <th style="width: 12%;" class="text-right">Rate</th>
                <th style="width: 13%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->lineItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="category-cell">{{ $item->category }}</td>
                <td>{{ truncate($item->description, 100) }}</td>
                <td class="text-center">{{ number_format($item->hours, 1) }}</td>
                <td class="text-right">{{ $quote->currency }} {{ number_format($item->rate, 2) }}</td>
                <td class="text-right">{{ $quote->currency }} {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Totals Section -->
    <div class="totals-section">
        <div class="totals-left">
            @if($quote->estimated_hours)
            <div style="font-size: 10px; color: #666;">
                <strong>Total Estimated Hours:</strong> {{ number_format($quote->estimated_hours, 1) }} hours
            </div>
            @endif
        </div>
        <div class="totals-right">
            <div class="totals-table">
                @if($quote->lineItems && $quote->lineItems->count() > 0)
                <div class="totals-row">
                    <div class="totals-label">Subtotal:</div>
                    <div class="totals-value">{{ $quote->currency }} {{ number_format($quote->lineItems->sum('total'), 2) }}</div>
                </div>
                @endif
                
                @if($quote->estimated_cost)
                <div class="totals-row total">
                    <div class="totals-label">Total:</div>
                    <div class="totals-value">{{ $quote->currency }} {{ number_format($quote->estimated_cost, 2) }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes/Terms -->
    <div class="notes-section">
        <div class="notes-title">Terms & Conditions</div>
        <div class="notes-content">
            • This quotation is valid until {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'the specified date' }}.<br>
            • Payment terms: To be discussed upon approval.<br>
            • The actual hours may vary based on project requirements and changes.<br>
            • All prices are in {{ $quote->currency }} and exclude applicable taxes unless stated otherwise.
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p style="margin-top: 5px;">Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
    </div>
</body>
</html>
