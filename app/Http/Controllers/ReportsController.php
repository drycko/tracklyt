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

class ReportsController extends Controller
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
     * Show the reports dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = $this->calculateReportStats();
        return view('reports.index', $stats);
    }

    private function calculateReportStats()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $tenant = auth()->user()->tenant;

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
        
        // Monthly time data (last 12 months for reports)
        $monthlyTimeData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $hours = TimeEntry::whereBetween('start_time', [$monthStart, $monthEnd])
                ->sum('duration_minutes') / 60;
            $billableHours = TimeEntry::whereBetween('start_time', [$monthStart, $monthEnd])
                ->where('is_billable', true)
                ->sum('duration_minutes') / 60;
            
            $monthlyTimeData[] = [
                'month' => $month->format('M Y'),
                'hours' => round($hours, 1),
                'billable' => round($billableHours, 1),
                'non_billable' => round($hours - $billableHours, 1),
            ];
        }
        
        // Monthly revenue data (last 12 months)
        $monthlyRevenueData = [];
        for ($i = 11; $i >= 0; $i--) {
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
        
        // Project stats (all projects with time tracked)
        $projectStats = Project::with('client')
            ->withSum('timeEntries', 'duration_minutes')
            ->having('time_entries_sum_duration_minutes', '>', 0)
            ->orderByDesc('time_entries_sum_duration_minutes')
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client' => $project->client->name,
                    'hours' => round(($project->time_entries_sum_duration_minutes ?? 0) / 60, 1),
                    'is_active' => $project->is_active,
                ];
            });
        
        // Client profitability
        $clientStats = Client::with(['projects.timeEntries', 'invoices.payments'])
            ->get()
            ->map(function($client) {
                $totalHours = $client->projects->sum(function($project) {
                    return $project->timeEntries->sum('duration_minutes');
                }) / 60;
                $totalRevenue = $client->invoices->where('status', 'paid')->sum('total');
                $totalOutstanding = $client->invoices->whereIn('status', ['sent', 'overdue'])->sum(function($invoice) {
                    return $invoice->balance_due;
                });
                
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'projects_count' => $client->projects->count(),
                    'hours' => round($totalHours, 1),
                    'revenue' => $totalRevenue,
                    'outstanding' => $totalOutstanding,
                ];
            })
            ->where('hours', '>', 0)
            ->sortByDesc('revenue')
            ->values();
        
        // User productivity (if admin)
        $userStats = collect([]);
        if (auth()->user()->isAdmin()) {
            $userStats = DB::table('time_entries')
                ->join('users', 'time_entries.user_id', '=', 'users.id')
                ->select('users.id', 'users.name')
                ->selectRaw('SUM(time_entries.duration_minutes) / 60 as total_hours')
                ->selectRaw('SUM(CASE WHEN time_entries.is_billable = 1 THEN time_entries.duration_minutes ELSE 0 END) / 60 as billable_hours')
                ->groupBy('users.id', 'users.name')
                ->having('total_hours', '>', 0)
                ->orderByDesc('total_hours')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'total_hours' => round($user->total_hours, 1),
                        'billable_hours' => round($user->billable_hours, 1),
                        'non_billable_hours' => round($user->total_hours - $user->billable_hours, 1),
                        'billable_percentage' => $user->total_hours > 0 ? round(($user->billable_hours / $user->total_hours) * 100, 1) : 0,
                    ];
                });
        }

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
            'clientStats' => $clientStats,
            'userStats' => $userStats,
            'retainerProfiles' => $retainerProfiles,
            'tenant' => $tenant,
        ];
    }
}
