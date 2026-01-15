<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\Invoice;
use App\Models\MaintenanceProfile;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Central users (super admin and support agents) should go to admin dashboard
            if ($user->isCentralUser()) {
                return redirect()->route('admin.dashboard');
            }

            $appCurrency = config('app.currency', 'USD');

            $stats = $this->calculateDashboardStats();

            return view('home', $stats);
        } catch (\Exception $e) {
            // Log the error and show a simple dashboard
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            return view('home', array_merge(
                $this->getEmptyStats(),
                ['recentTimeEntries' => collect([])]
            ));
        }
    }

    private function getEmptyStats()
    {
        $user = auth()->user();
        $appCurrency = config('app.currency', 'USD');
        //if user belong to a tenant, get tenant currency
        if ($user->tenant) {
            $appCurrency = $user->tenant->currency ?? $appCurrency;
        }
        return [
            'totalClients' => 0,
            'activeProjects' => 0,
            'hoursThisMonth' => 0,
            'pendingInvoices' => 0,
            'totalRevenue' => 0,
            'outstandingAmount' => 0,
            'billableHoursThisMonth' => 0,
            'nonBillableHoursThisMonth' => 0,
            'activeRetainers' => 0,
            'retainerUsagePercentage' => 0,
            'revenueThisMonth' => 0,
            'revenueLastMonth' => 0,
            'hoursLastMonth' => 0,
            'monthlyTimeData' => [],
            'monthlyRevenueData' => [],
            'projectStats' => collect([]),
            'retainerProfiles' => collect([]),
            'recentQuotes' => collect([]),
            'overdueInvoices' => collect([]),
            'appCurrency' => $appCurrency
        ];
    }

    private function calculateDashboardStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // tenant currency
        $appCurrency = auth()->user()->tenant->currency ?? 'USD';

        // Basic counts
        $totalClients = Client::count();
        $activeProjects = Project::where('is_active', true)->count();
        
        // Time tracking stats
        $hoursThisMonth = TimeEntry::whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->sum('duration_minutes') / 60;
        $hoursLastMonth = TimeEntry::whereBetween('start_time', [$startOfLastMonth, $endOfLastMonth])
            ->sum('duration_minutes') / 60;
        
        // Billable vs non-billable
        $billableHoursThisMonth = TimeEntry::whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->where('is_billable', true)
            ->sum('duration_minutes') / 60;
        $nonBillableHoursThisMonth = $hoursThisMonth - $billableHoursThisMonth;
        
        // Invoice stats
        $pendingInvoices = Invoice::whereIn('status', ['draft', 'sent'])->count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        
        // Calculate outstanding amount (total - total_paid for unpaid invoices)
        $outstandingInvoices = Invoice::with('payments')
            ->whereIn('status', ['sent', 'overdue'])
            ->get();
        $outstandingAmount = $outstandingInvoices->sum(function($invoice) {
            return $invoice->balance_due;
        });
        
        // Revenue by month
        $revenueThisMonth = Invoice::where('status', 'paid')
            ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
            ->sum('total');
        $revenueLastMonth = Invoice::where('status', 'paid')
            ->whereBetween('issue_date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total');
        
        // Retainer stats
        $retainerProfiles = MaintenanceProfile::with('project.client')
            ->where('maintenance_type', 'retainer')
            ->get();
        $activeRetainers = $retainerProfiles->count();
        $avgUsage = $retainerProfiles->avg('usage_percentage') ?? 0;
        
        // Monthly time data (last 6 months)
        $monthlyTimeData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $hours = TimeEntry::whereBetween('start_time', [$monthStart, $monthEnd])
                ->sum('duration_minutes') / 60;
            
            $monthlyTimeData[] = [
                'month' => $month->format('M Y'),
                'hours' => round($hours, 1),
            ];
        }
        
        // Monthly revenue data (last 6 months)
        $monthlyRevenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Invoice::where('status', 'paid')
                ->whereBetween('issue_date', [$monthStart, $monthEnd])
                ->sum('total');
            
            $monthlyRevenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        
        // Project stats (top 5 by hours)
        $projectStats = Project::with('client')
            ->withCount(['timeEntries' => function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_time', [$startOfMonth, $endOfMonth]);
            }])
            ->withSum(['timeEntries' => function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_time', [$startOfMonth, $endOfMonth]);
            }], 'duration_minutes')
            ->having('time_entries_sum_duration_minutes', '>', 0)
            ->orderByDesc('time_entries_sum_duration_minutes')
            ->take(5)
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client' => $project->client->name,
                    'hours' => round(($project->time_entries_sum_duration_minutes ?? 0) / 60, 1),
                    'entries_count' => $project->time_entries_count,
                ];
            });
        
        // Recent quotes
        $recentQuotes = Quote::with('client')
            ->latest()
            ->take(5)
            ->get();
        
        // Overdue invoices
        $overdueInvoices = Invoice::with('client')
            ->where('status', 'sent')
            ->where('due_date', '<', $now)
            ->orderBy('due_date')
            ->get();
        
        // Recent time entries
        $recentTimeEntries = TimeEntry::with(['task', 'user', 'project'])
            ->latest('start_time')
            ->take(10)
            ->get();

        return [
            'totalClients' => $totalClients,
            'activeProjects' => $activeProjects,
            'hoursThisMonth' => $hoursThisMonth,
            'hoursLastMonth' => $hoursLastMonth,
            'billableHoursThisMonth' => $billableHoursThisMonth,
            'nonBillableHoursThisMonth' => $nonBillableHoursThisMonth,
            'pendingInvoices' => $pendingInvoices,
            'totalRevenue' => $totalRevenue,
            'outstandingAmount' => $outstandingAmount,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'activeRetainers' => $activeRetainers,
            'retainerUsagePercentage' => $avgUsage,
            'monthlyTimeData' => $monthlyTimeData,
            'monthlyRevenueData' => $monthlyRevenueData,
            'projectStats' => $projectStats,
            'retainerProfiles' => $retainerProfiles,
            'recentQuotes' => $recentQuotes,
            'overdueInvoices' => $overdueInvoices,
            'recentTimeEntries' => $recentTimeEntries,
            'appCurrency' => $appCurrency,
        ];
    }
}
