<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectLink;
use Illuminate\Http\Request;

class ProjectLinkController extends Controller
{
    /**
     * Display a listing of the resource for a project.
     */
    public function index(Project $project)
    {
        $links = $project->links;
        return view('projects.links.index', compact('project', 'links'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('projects.links.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'type' => 'required|in:demo,staging,production',
            'label' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $validated['project_id'] = $project->id;

        $link = ProjectLink::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project link added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectLink $link)
    {
        return view('projects.links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectLink $link)
    {
        $validated = $request->validate([
            'type' => 'required|in:demo,staging,production',
            'label' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $link->update($validated);

        return redirect()->route('projects.show', $link->project)
            ->with('success', 'Project link updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectLink $link)
    {
        $project = $link->project;
        $link->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project link deleted successfully.');
    }
}
