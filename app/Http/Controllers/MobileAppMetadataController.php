<?php

namespace App\Http\Controllers;

use App\Models\MobileAppMetadata;
use App\Models\Project;
use Illuminate\Http\Request;

class MobileAppMetadataController extends Controller
{
    /**
     * Display a listing of the resource for a project.
     */
    public function index(Project $project)
    {
        $mobileApps = $project->mobileApps;
        return view('projects.mobile-apps.index', compact('project', 'mobileApps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('projects.mobile-apps.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'platform' => 'required|in:android,ios',
            'app_name' => 'required|string|max:255',
            'package_name' => 'nullable|string|max:255',
            'app_store_url' => 'nullable|url',
            'play_store_url' => 'nullable|url',
            'current_version' => 'nullable|string|max:50',
        ]);

        $validated['project_id'] = $project->id;

        $mobileApp = MobileAppMetadata::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Mobile app metadata added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, MobileAppMetadata $mobileApp)
    {
        return view('projects.mobile-apps.show', compact('project', 'mobileApp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MobileAppMetadata $mobileApp)
    {
        return view('projects.mobile-apps.edit', compact('mobileApp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MobileAppMetadata $mobileApp)
    {
        $validated = $request->validate([
            'platform' => 'required|in:android,ios',
            'app_name' => 'required|string|max:255',
            'package_name' => 'nullable|string|max:255',
            'app_store_url' => 'nullable|url',
            'play_store_url' => 'nullable|url',
            'current_version' => 'nullable|string|max:50',
        ]);

        $mobileApp->update($validated);

        return redirect()->route('projects.show', $mobileApp->project)
            ->with('success', 'Mobile app metadata updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MobileAppMetadata $mobileApp)
    {
        $project = $mobileApp->project;
        $mobileApp->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Mobile app metadata deleted successfully.');
    }
}
