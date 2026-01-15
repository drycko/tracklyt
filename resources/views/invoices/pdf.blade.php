<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            padding: 40px;
        }
        .header {
            border-bottom: 3px solid #2C3E50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2C3E50;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #2C3E50;
            text-align: right;
            margin-top: -40px;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 13px;
            color: #333;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        thead {
            background-color: #2C3E50;
            color: white;
        }
        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tbody tr:last-child td {
            border-bottom: 2px solid #2C3E50;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            width: 40%;
            float: right;
        }
        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .totals-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-weight: bold;
        }
        .totals-value {
            display: table-cell;
            text-align: right;
            width: 120px;
        }
        .total-final {
            font-size: 18px;
            color: #2C3E50;
            padding-top: 10px;
            border-top: 2px solid #2C3E50;
        }
        .notes {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #666;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #95a5a6; color: white; }
        .status-sent { background-color: #3498db; color: white; }
        .status-paid { background-color: #27ae60; color: white; }
        .status-overdue { background-color: #e74c3c; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if(auth()->user()->tenant && auth()->user()->tenant->company_logo)
                <img src="{{ public_path('storage/' . auth()->user()->tenant->company_logo) }}" alt="Logo" class="company-logo">
            @else
                <img src="{{ public_path('assets/images/tracklyt-logo-drk-wide.png') }}" alt="Tracklyt Logo" class="company-logo">
            @endif
            <div class="company-name">{{ auth()->user()->tenant->name ?? 'Tracklyt' }}</div>
            <div class="invoice-title">INVOICE</div>
        </div>

        <!-- Invoice Info and Client Details -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-col">
                    <div class="info-label">Invoice Number</div>
                    <div class="info-value" style="font-size: 16px; font-weight: bold;">{{ $invoice->invoice_number }}</div>
                    
                    <div class="info-label" style="margin-top: 15px;">Issue Date</div>
                    <div class="info-value">{{ $invoice->issue_date->format('F d, Y') }}</div>
                    
                    <div class="info-label" style="margin-top: 10px;">Due Date</div>
                    <div class="info-value">{{ $invoice->due_date->format('F d, Y') }}</div>

                    <div style="margin-top: 15px;">
                        @if($invoice->status === 'draft')
                        <span class="status-badge status-draft">Draft</span>
                        @elseif($invoice->status === 'sent')
                        <span class="status-badge status-sent">Sent</span>
                        @elseif($invoice->status === 'paid')
                        <span class="status-badge status-paid">Paid</span>
                        @elseif($invoice->status === 'overdue')
                        <span class="status-badge status-overdue">Overdue</span>
                        @endif
                    </div>
                </div>
                
                <div class="info-col" style="text-align: right;">
                    <div class="info-label">Bill To</div>
                    <div class="info-value" style="font-size: 16px; font-weight: bold;">{{ $invoice->client->name }}</div>
                    @if($invoice->client->email)
                    <div class="info-value">{{ $invoice->client->email }}</div>
                    @endif
                    @if($invoice->client->phone)
                    <div class="info-value">{{ $invoice->client->phone }}</div>
                    @endif
                    
                    @if($invoice->project)
                    <div class="info-label" style="margin-top: 15px;">Project</div>
                    <div class="info-value">{{ $invoice->project->name }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 15%;" class="text-center">Quantity</th>
                    <th style="width: 15%;" class="text-right">Rate</th>
                    <th style="width: 15%;" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ format_money($item->unit_price, $invoice->currency, false) }}</td>
                    <td class="text-right">{{ format_money($item->total, $invoice->currency, false) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">{{ format_money($invoice->subtotal, $invoice->currency, false) }}</div>
            </div>
            @if($invoice->tax > 0)
            <div class="totals-row">
                <div class="totals-label">Tax:</div>
                <div class="totals-value">{{ format_money($invoice->tax, $invoice->currency) }}</div>
            </div>
            @endif
            <div class="totals-row total-final">
                <div class="totals-label">TOTAL:</div>
                <div class="totals-value">{{ format_money($invoice->total, $invoice->currency) }}</div>
            </div>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">Notes / Terms</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div>Thank you for your business!</div>
            <div style="margin-top: 5px;">Invoice generated on {{ now()->format('F d, Y') }}</div>
        </div>
    </div>
</body>
</html>
