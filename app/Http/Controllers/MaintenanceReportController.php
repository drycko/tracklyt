<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceReport;
use App\Models\MaintenanceReportType;
use App\Models\MaintenanceReportTask;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaintenanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaintenanceReport::with(['project', 'reportType', 'assignedTo']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by type
        if ($request->filled('type_id')) {
            $query->where('report_type_id', $request->type_id);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $reports = $query->latest('created_at')->paginate(15);

        $projects = Project::active()->get();
        $types = MaintenanceReportType::all();

        return view('maintenance-reports.index', compact('reports', 'projects', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::active()->get();
        $types = MaintenanceReportType::with('taskTemplates')->get();

        return view('maintenance-reports.create', compact('projects', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'maintenance_report_type_id' => 'required|exists:maintenance_report_types,id',
            'assigned_to' => 'nullable|exists:users,id',
            'scheduled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create the report
            $report = MaintenanceReport::create([
                'tenant_id' => auth()->user()->tenant_id,
                'project_id' => $validated['project_id'],
                'report_type_id' => $validated['maintenance_report_type_id'],
                'report_number' => MaintenanceReport::generateReportNumber(),
                'status' => 'draft',
                'assigned_to' => $validated['assigned_to'] ?? auth()->id(),
                'scheduled_date' => $validated['scheduled_date'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            // Load tasks from template
            $type = MaintenanceReportType::with('taskTemplates')->find($validated['maintenance_report_type_id']);
            
            foreach ($type->taskTemplates as $template) {
                MaintenanceReportTask::create([
                    'tenant_id' => auth()->user()->tenant_id,
                    'maintenance_report_id' => $report->id,
                    'task_name' => $template->task_name,
                    'task_description' => $template->task_description,
                    'estimated_time_minutes' => $template->estimated_time_minutes,
                    'display_order' => $template->display_order,
                    'is_completed' => false,
                ]);
            }

            DB::commit();

            return redirect()->route('maintenance-reports.show', $report)
                ->with('success', 'Maintenance report created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create maintenance report: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load(['project', 'reportType', 'assignedTo', 'createdBy', 'tasks' => function ($query) {
            $query->orderBy('display_order');
        }]);

        return view('maintenance-reports.show', compact('maintenanceReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceReport $maintenanceReport)
    {
        if (!in_array($maintenanceReport->status, ['draft', 'in_progress'])) {
            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('error', 'Only draft or in-progress reports can be edited.');
        }

        $projects = Project::active()->get();
        $types = MaintenanceReportType::all();

        return view('maintenance-reports.edit', compact('maintenanceReport', 'projects', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceReport $maintenanceReport)
    {
        if (!in_array($maintenanceReport->status, ['draft', 'in_progress'])) {
            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('error', 'Only draft or in-progress reports can be edited.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'scheduled_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $maintenanceReport->update($validated);

        return redirect()->route('maintenance-reports.show', $maintenanceReport)
            ->with('success', 'Maintenance report updated successfully.');
    }

    /**
     * Start the maintenance report.
     */
    public function start(MaintenanceReport $maintenanceReport)
    {
        if ($maintenanceReport->status !== 'draft') {
            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('error', 'Only draft reports can be started.');
        }

        $maintenanceReport->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('maintenance-reports.show', $maintenanceReport)
            ->with('success', 'Maintenance report started.');
    }

    /**
     * Complete the maintenance report.
     */
    public function complete(MaintenanceReport $maintenanceReport)
    {
        if ($maintenanceReport->status !== 'in_progress') {
            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('error', 'Only in-progress reports can be completed.');
        }

        // Check if all tasks are completed
        $incompleteTasks = $maintenanceReport->tasks()->where('is_completed', false)->count();
        
        if ($incompleteTasks > 0) {
            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('error', "Cannot complete report. {$incompleteTasks} task(s) still incomplete.");
        }

        $maintenanceReport->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('maintenance-reports.show', $maintenanceReport)
            ->with('success', 'Maintenance report completed successfully!');
    }

    /**
     * Update a task in the report.
     */
    public function updateTask(Request $request, MaintenanceReport $maintenanceReport, MaintenanceReportTask $task)
    {
        if ($task->maintenance_report_id !== $maintenanceReport->id) {
            abort(404);
        }

        $validated = $request->validate([
            'is_completed' => 'nullable|boolean',
            'comments' => 'nullable|string',
            'time_spent_minutes' => 'nullable|integer|min:0',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|max:5120', // 5MB max
        ]);

        try {
            DB::beginTransaction();

            // Handle screenshots upload
            $screenshots = $task->screenshots ?? [];
            
            if ($request->hasFile('screenshots')) {
                foreach ($request->file('screenshots') as $file) {
                    $path = $file->store('maintenance-screenshots', 'public');
                    $screenshots[] = $path;
                }
            }

            // Determine is_completed status
            $isCompleted = isset($validated['is_completed']) ? (bool)$validated['is_completed'] : $task->is_completed;

            $task->update([
                'is_completed' => $isCompleted,
                'comments' => $validated['comments'],
                'time_spent_minutes' => $validated['time_spent_minutes'],
                'screenshots' => $screenshots,
                'completed_at' => $isCompleted ? ($task->completed_at ?? now()) : null,
            ]);

            // Update report completion percentage
            $maintenanceReport->updateCompletionPercentage();

            DB::commit();

            return redirect()->route('maintenance-reports.show', $maintenanceReport)
                ->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Delete a screenshot from a task.
     */
    public function deleteScreenshot(MaintenanceReport $maintenanceReport, MaintenanceReportTask $task, $screenshotIndex)
    {
        if ($task->maintenance_report_id !== $maintenanceReport->id) {
            abort(404);
        }

        $screenshots = $task->screenshots ?? [];
        
        if (isset($screenshots[$screenshotIndex])) {
            // Delete file from storage
            Storage::disk('public')->delete($screenshots[$screenshotIndex]);
            
            // Remove from array
            unset($screenshots[$screenshotIndex]);
            $screenshots = array_values($screenshots); // Re-index array
            
            $task->update(['screenshots' => $screenshots]);

            return redirect()->back()
                ->with('success', 'Screenshot deleted successfully.');
        }

        return redirect()->back()
            ->with('error', 'Screenshot not found.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceReport $maintenanceReport)
    {
        if (in_array($maintenanceReport->status, ['completed', 'sent'])) {
            return redirect()->route('maintenance-reports.index')
                ->with('error', 'Cannot delete completed or sent reports.');
        }

        // Delete all task screenshots
        foreach ($maintenanceReport->tasks as $task) {
            if ($task->screenshots) {
                foreach ($task->screenshots as $screenshot) {
                    Storage::disk('public')->delete($screenshot);
                }
            }
        }

        $maintenanceReport->delete();

        return redirect()->route('maintenance-reports.index')
            ->with('success', 'Maintenance report deleted successfully.');
    }

    /**
     * Download PDF of the maintenance report.
     */
    public function downloadPdf(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load(['project', 'reportType', 'tasks' => function($query) {
            $query->orderBy('display_order');
        }, 'createdBy', 'assignedTo']);

        $pdf = Pdf::loadView('maintenance-reports.pdf', [
            'report' => $maintenanceReport
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($maintenanceReport->report_number . '.pdf');
    }
}
