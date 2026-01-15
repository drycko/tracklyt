<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Http\Request;

class ProjectRepositoryController extends Controller
{
    /**
     * Display a listing of the resource for a project.
     */
    public function index(Project $project)
    {
        $repositories = $project->repositories;
        return view('projects.repositories.index', compact('project', 'repositories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        return view('projects.repositories.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'provider' => 'required|in:github,gitlab,bitbucket',
            'repo_url' => 'required|url',
            'is_primary' => 'boolean',
        ]);

        $validated['project_id'] = $project->id;

        $repository = ProjectRepository::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Repository added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectRepository $repository)
    {
        return view('projects.repositories.edit', compact('repository'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectRepository $repository)
    {
        $validated = $request->validate([
            'provider' => 'required|in:github,gitlab,bitbucket',
            'repo_url' => 'required|url',
            'is_primary' => 'boolean',
        ]);

        $repository->update($validated);

        return redirect()->route('projects.show', $repository->project)
            ->with('success', 'Repository updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectRepository $repository)
    {
        $project = $repository->project;
        $repository->delete();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Repository deleted successfully.');
    }
}
