<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Quote;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with('client', 'quote');

        // Filter by status (active/inactive)
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by project type
        if ($request->has('type')) {
            $query->where('project_type', $request->type);
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query->latest()->paginate(15);
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::active()->get();
        $quotes = Quote::approved()->with('client')->get();
        
        return view('projects.create', compact('clients', 'quotes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_type' => 'required|in:new_build,maintenance_takeover',
            'source' => 'required|in:quote,direct',
            'billing_type' => 'required|in:hourly,fixed,retainer',
            'hourly_rate' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load('client', 'quote', 'repositories', 'links', 'tasks', 'timeEntries', 'mobileApps');
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $clients = Client::active()->get();
        $quotes = Quote::approved()->with('client')->get();
        
        return view('projects.edit', compact('project', 'clients', 'quotes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_type' => 'required|in:new_build,maintenance_takeover',
            'source' => 'required|in:quote,direct',
            'billing_type' => 'required|in:hourly,fixed,retainer',
            'hourly_rate' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Create project from approved quote.
     */
    public function createFromQuote(Quote $quote)
    {
        if (!$quote->isApproved()) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'Only approved quotes can be converted to projects.');
        }

        $project = Project::create([
            'client_id' => $quote->client_id,
            'quote_id' => $quote->id,
            'name' => $quote->title,
            'description' => $quote->description,
            'project_type' => 'new_build',
            'source' => 'quote',
            'billing_type' => 'fixed',
            'estimated_hours' => $quote->estimated_hours,
            'hourly_rate' => null,
            'is_active' => true,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created from quote successfully.');
    }
}
