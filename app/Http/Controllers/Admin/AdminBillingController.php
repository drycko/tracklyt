<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminBillingController extends Controller
{
    /**
     * Show billing overview and revenue analytics.
     */
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Calculate MRR for selected month
        $mrr = $this->calculateMRR($year, $month);
        $arr = $mrr * 12;

        // Revenue breakdown by plan
        $revenueByPlan = $this->getRevenueByPlan($year, $month);

        // Revenue trend (last 12 months)
        $revenueTrend = $this->getRevenueTrend($year, $month);

        // Churn analysis
        $churnStats = $this->getChurnStats($year, $month);

        // Billing cycle breakdown
        $billingCycleStats = $this->getBillingCycleStats();

        // Recent transactions (when Stripe is integrated)
        $recentTransactions = []; // Placeholder for Stripe transactions

        return view('admin.billing.index', compact(
            'mrr',
            'arr',
            'revenueByPlan',
            'revenueTrend',
            'churnStats',
            'billingCycleStats',
            'recentTransactions',
            'year',
            'month'
        ));
    }

    /**
     * Calculate Monthly Recurring Revenue.
     */
    protected function calculateMRR(int $year, int $month): float
    {
        $monthlyMRR = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'monthly')
            ->whereYear('tenant_subscriptions.current_period_start', '<=', $year)
            ->whereMonth('tenant_subscriptions.current_period_start', '<=', $month)
            ->join('subscription_plans', 'tenant_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price_monthly');

        $yearlyMRR = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'yearly')
            ->whereYear('tenant_subscriptions.current_period_start', '<=', $year)
            ->whereMonth('tenant_subscriptions.current_period_start', '<=', $month)
            ->join('subscription_plans', 'tenant_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->sum('subscription_plans.price_yearly');

        return $monthlyMRR + ($yearlyMRR / 12);
    }

    /**
     * Get revenue breakdown by plan.
     */
    protected function getRevenueByPlan(int $year, int $month): array
    {
        $subscriptions = TenantSubscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->groupBy('subscription_plan_id');

        $revenue = [];
        foreach ($subscriptions as $planId => $subs) {
            $plan = $subs->first()->plan;
            $monthlyRevenue = $subs->where('billing_cycle', 'monthly')->count() * $plan->price_monthly;
            $yearlyRevenue = $subs->where('billing_cycle', 'yearly')->count() * ($plan->price_yearly / 12);
            
            $revenue[$plan->name] = [
                'count' => $subs->count(),
                'mrr' => $monthlyRevenue + $yearlyRevenue,
                'arr' => ($monthlyRevenue + $yearlyRevenue) * 12,
            ];
        }

        return $revenue;
    }

    /**
     * Get revenue trend for last 12 months.
     */
    protected function getRevenueTrend(int $currentYear, int $currentMonth): array
    {
        $trend = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mrr = $this->calculateMRR($date->year, $date->month);
            
            $trend[$date->format('M Y')] = [
                'mrr' => $mrr,
                'arr' => $mrr * 12,
            ];
        }

        return $trend;
    }

    /**
     * Get churn statistics.
     */
    protected function getChurnStats(int $year, int $month): array
    {
        $startOfMonth = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Subscriptions at start of month
        $startCount = TenantSubscription::where('status', 'active')
            ->where('created_at', '<', $startOfMonth)
            ->count();

        // New subscriptions this month
        $newCount = TenantSubscription::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Canceled this month
        $canceledCount = TenantSubscription::where('status', 'canceled')
            ->whereBetween('canceled_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Churn rate
        $churnRate = $startCount > 0 ? ($canceledCount / $startCount) * 100 : 0;

        return [
            'start_count' => $startCount,
            'new_count' => $newCount,
            'canceled_count' => $canceledCount,
            'churn_rate' => round($churnRate, 2),
        ];
    }

    /**
     * Get billing cycle statistics.
     */
    protected function getBillingCycleStats(): array
    {
        $monthly = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'monthly')
            ->count();

        $yearly = TenantSubscription::where('tenant_subscriptions.status', 'active')
            ->where('tenant_subscriptions.billing_cycle', 'yearly')
            ->count();

        return [
            'monthly' => $monthly,
            'yearly' => $yearly,
            'total' => $monthly + $yearly,
        ];
    }

    /**
     * Show detailed revenue report.
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonths(6)->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Daily revenue breakdown
        $dailyRevenue = $this->getDailyRevenue($startDate, $endDate);

        // Plan performance
        $planPerformance = $this->getPlanPerformance($startDate, $endDate);

        // Customer lifetime value
        $customerLTV = $this->calculateCustomerLTV();

        return view('admin.billing.report', compact(
            'dailyRevenue',
            'planPerformance',
            'customerLTV',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get daily revenue.
     */
    protected function getDailyRevenue($startDate, $endDate): array
    {
        // This would track actual payment dates when Stripe is integrated
        // For now, return estimated daily MRR
        return [];
    }

    /**
     * Get plan performance metrics.
     */
    protected function getPlanPerformance($startDate, $endDate): array
    {
        $plans = SubscriptionPlan::withCount([
            'subscriptions as new_subscriptions' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'subscriptions as canceled_subscriptions' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'canceled')
                    ->whereBetween('canceled_at', [$startDate, $endDate]);
            },
        ])->get();

        return $plans->map(function ($plan) {
            return [
                'name' => $plan->name,
                'new' => $plan->new_subscriptions,
                'canceled' => $plan->canceled_subscriptions,
                'net_growth' => $plan->new_subscriptions - $plan->canceled_subscriptions,
            ];
        })->toArray();
    }

    /**
     * Calculate average customer lifetime value.
     */
    protected function calculateCustomerLTV(): float
    {
        // Simple LTV = Average MRR per customer * Average customer lifetime in months
        $avgMRR = TenantSubscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->avg(function ($subscription) {
                return $subscription->billing_cycle === 'yearly'
                    ? $subscription->plan->price_yearly / 12
                    : $subscription->plan->price_monthly;
            });

        // Estimate average lifetime as 12 months (this should be calculated from actual data)
        $avgLifetimeMonths = 12;

        return $avgMRR * $avgLifetimeMonths;
    }

    /**
     * Export billing data.
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Gather billing data
        $mrr = $this->calculateMRR($year, $month);
        $arr = $mrr * 12;
        $revenueByPlan = $this->getRevenueByPlan($year, $month);
        $churnStats = $this->getChurnStats($year, $month);
        $billingCycleStats = $this->getBillingCycleStats();
        $customerLTV = $this->calculateCustomerLTV();

        if ($format === 'pdf') {
            // Generate PDF report
            $pdf = Pdf::loadView('admin.billing.export-pdf', compact(
                'mrr',
                'arr',
                'revenueByPlan',
                'churnStats',
                'billingCycleStats',
                'customerLTV',
                'year',
                'month'
            ));

            return $pdf->download('billing-report-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
        } elseif ($format === 'csv') {
            // Generate CSV export
            return $this->exportCSV($revenueByPlan, $churnStats, $year, $month);
        } else {
            return back()->with('info', 'Excel export coming soon. Please use PDF or CSV for now.');
        }
    }

    /**
     * Export billing data as CSV.
     */
    protected function exportCSV($revenueByPlan, $churnStats, $year, $month)
    {
        $filename = 'billing-report-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($revenueByPlan, $churnStats) {
            $file = fopen('php://output', 'w');
            
            // Revenue by Plan section
            fputcsv($file, ['Revenue by Plan']);
            fputcsv($file, ['Plan Name', 'Subscriptions', 'MRR', 'ARR']);
            
            foreach ($revenueByPlan as $planName => $data) {
                fputcsv($file, [
                    $planName,
                    $data['count'],
                    number_format($data['mrr'], 2),
                    number_format($data['arr'], 2)
                ]);
            }
            
            // Empty row
            fputcsv($file, []);
            
            // Churn Statistics section
            fputcsv($file, ['Churn Statistics']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Start Count', $churnStats['start_count']]);
            fputcsv($file, ['New Subscriptions', $churnStats['new_count']]);
            fputcsv($file, ['Canceled', $churnStats['canceled_count']]);
            fputcsv($file, ['Churn Rate (%)', $churnStats['churn_rate']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
