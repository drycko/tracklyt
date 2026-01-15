<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Report - {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .metrics {
            margin-bottom: 30px;
        }
        .metric-row {
            display: flex;
            margin-bottom: 15px;
        }
        .metric-box {
            flex: 1;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            margin-right: 10px;
        }
        .metric-box:last-child {
            margin-right: 0;
        }
        .metric-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .stats-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-item {
            flex: 1;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Billing & Revenue Report</h1>
        <p>{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</p>
        <p>Generated on {{ now()->format('F d, Y g:i A') }}</p>
    </div>

    <!-- Key Metrics -->
    <div class="metrics">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 25%;">
                    <div class="metric-box">
                        <div class="metric-label">Monthly Recurring Revenue</div>
                        <div class="metric-value">{{ format_money($mrr) }}</div>
                    </div>
                </td>
                <td style="border: none; width: 25%;">
                    <div class="metric-box">
                        <div class="metric-label">Annual Recurring Revenue</div>
                        <div class="metric-value">{{ format_money($arr) }}</div>
                    </div>
                </td>
                <td style="border: none; width: 25%;">
                    <div class="metric-box">
                        <div class="metric-label">Churn Rate</div>
                        <div class="metric-value">{{ $churnStats['churn_rate'] }}%</div>
                    </div>
                </td>
                <td style="border: none; width: 25%;">
                    <div class="metric-box">
                        <div class="metric-label">Customer LTV</div>
                        <div class="metric-value">{{ format_money($customerLTV) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Revenue by Plan -->
    <div class="section">
        <div class="section-title">Revenue by Plan</div>
        @if(empty($revenueByPlan))
            <p>No revenue data available for this period.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th class="text-center">Subscriptions</th>
                        <th class="text-right">Monthly Recurring Revenue</th>
                        <th class="text-right">Annual Recurring Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueByPlan as $planName => $data)
                        <tr>
                            <td>{{ $planName }}</td>
                            <td class="text-center">{{ $data['count'] }}</td>
                            <td class="text-right">{{ format_money($data['mrr']) }}</td>
                            <td class="text-right">{{ format_money($data['arr']) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background: #f8f9fa; font-weight: bold;">
                        <td>TOTAL</td>
                        <td class="text-center">{{ array_sum(array_column($revenueByPlan, 'count')) }}</td>
                        <td class="text-right">{{ format_money(array_sum(array_column($revenueByPlan, 'mrr'))) }}</td>
                        <td class="text-right">{{ format_money(array_sum(array_column($revenueByPlan, 'arr'))) }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

    <!-- Subscription Statistics -->
    <div class="section">
        <div class="section-title">Subscription Changes</div>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th class="text-right">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Subscriptions at Start of Month</td>
                    <td class="text-right">{{ number_format($churnStats['start_count']) }}</td>
                </tr>
                <tr>
                    <td>New Subscriptions</td>
                    <td class="text-right" style="color: #28a745;">+{{ number_format($churnStats['new_count']) }}</td>
                </tr>
                <tr>
                    <td>Canceled Subscriptions</td>
                    <td class="text-right" style="color: #dc3545;">-{{ number_format($churnStats['canceled_count']) }}</td>
                </tr>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>Net Change</td>
                    <td class="text-right" style="color: {{ ($churnStats['new_count'] - $churnStats['canceled_count']) >= 0 ? '#28a745' : '#dc3545' }};">
                        {{ ($churnStats['new_count'] - $churnStats['canceled_count']) >= 0 ? '+' : '' }}{{ number_format($churnStats['new_count'] - $churnStats['canceled_count']) }}
                    </td>
                </tr>
                <tr>
                    <td>Churn Rate</td>
                    <td class="text-right">{{ $churnStats['churn_rate'] }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Billing Cycle Distribution -->
    <div class="section">
        <div class="section-title">Billing Cycle Distribution</div>
        <table>
            <thead>
                <tr>
                    <th>Billing Cycle</th>
                    <th class="text-center">Count</th>
                    <th class="text-right">Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monthly Billing</td>
                    <td class="text-center">{{ number_format($billingCycleStats['monthly']) }}</td>
                    <td class="text-right">
                        {{ $billingCycleStats['total'] > 0 ? number_format(($billingCycleStats['monthly'] / $billingCycleStats['total']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr>
                    <td>Yearly Billing</td>
                    <td class="text-center">{{ number_format($billingCycleStats['yearly']) }}</td>
                    <td class="text-right">
                        {{ $billingCycleStats['total'] > 0 ? number_format(($billingCycleStats['yearly'] / $billingCycleStats['total']) * 100, 1) : 0 }}%
                    </td>
                </tr>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td>Total Active Subscriptions</td>
                    <td class="text-center">{{ number_format($billingCycleStats['total']) }}</td>
                    <td class="text-right">100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report is generated automatically by {{ config('app.name') }}</p>
        <p>Confidential - For Internal Use Only</p>
    </div>
</body>
</html>
