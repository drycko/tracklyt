<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceProfile;
use App\Models\Project;
use Illuminate\Http\Request;

class MaintenanceProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = MaintenanceProfile::with('project.client')
            ->latest()
            ->paginate(15);
        
        return view('maintenance-profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::active()
            ->whereDoesntHave('maintenanceProfile')
            ->get();
        
        return view('maintenance-profiles.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id|unique:maintenance_profiles,project_id',
            'maintenance_type' => 'required|in:retainer,hourly',
            'monthly_hours' => 'nullable|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'sla_notes' => 'nullable|string',
            'rollover_hours' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        // Validate monthly_hours is required for retainer type
        if ($validated['maintenance_type'] === 'retainer' && empty($validated['monthly_hours'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Monthly hours is required for retainer type.');
        }

        $profile = MaintenanceProfile::create($validated);

        return redirect()->route('maintenance-profiles.show', $profile)
            ->with('success', 'Maintenance profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenanceProfile $maintenanceProfile)
    {
        $maintenanceProfile->load('project.client');
        
        // Get current period time entries
        $timeEntries = $maintenanceProfile->currentPeriodTimeEntries()
            ->with('user', 'task')
            ->latest('start_time')
            ->get();
        
        return view('maintenance-profiles.show', compact('maintenanceProfile', 'timeEntries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceProfile $maintenanceProfile)
    {
        $projects = Project::active()->get();
        return view('maintenance-profiles.edit', compact('maintenanceProfile', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceProfile $maintenanceProfile)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id|unique:maintenance_profiles,project_id,' . $maintenanceProfile->id,
            'maintenance_type' => 'required|in:retainer,hourly',
            'monthly_hours' => 'nullable|numeric|min:0',
            'rate' => 'required|numeric|min:0',
            'sla_notes' => 'nullable|string',
            'rollover_hours' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        // Validate monthly_hours is required for retainer type
        if ($validated['maintenance_type'] === 'retainer' && empty($validated['monthly_hours'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Monthly hours is required for retainer type.');
        }

        $maintenanceProfile->update($validated);

        return redirect()->route('maintenance-profiles.show', $maintenanceProfile)
            ->with('success', 'Maintenance profile updated successfully.');
    }

    /**
     * Reset monthly retainer.
     */
    public function reset(MaintenanceProfile $maintenanceProfile)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only admins can reset maintenance profiles.');
        }

        if ($maintenanceProfile->maintenance_type !== 'retainer') {
            return redirect()->back()
                ->with('error', 'Only retainer profiles can be reset.');
        }

        $maintenanceProfile->resetMonthly();

        return redirect()->route('maintenance-profiles.show', $maintenanceProfile)
            ->with('success', 'Maintenance profile reset successfully. Rollover hours updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceProfile $maintenanceProfile)
    {
        $maintenanceProfile->delete();

        return redirect()->route('maintenance-profiles.index')
            ->with('success', 'Maintenance profile deleted successfully.');
    }
}
