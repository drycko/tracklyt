<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TimeEntry::with('user', 'project', 'task');

        // Filter by project
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by task
        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by billable
        if ($request->has('billable')) {
            $query->where('is_billable', $request->billable === 'yes');
        }

        // Filter by locked
        if ($request->has('locked')) {
            if ($request->locked === 'yes') {
                $query->locked();
            } else {
                $query->unlocked();
            }
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('start_time', '<=', $request->end_date);
        }

        $timeEntries = $query->latest('start_time')->paginate(15);
        
        return view('time-entries.index', compact('timeEntries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::active()->get();
        $tasks = Task::all();
        
        return view('time-entries.create', compact('projects', 'tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_billable' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // make sure task belongs to project
        $task = Task::find($validated['task_id']);
        if ($task->project_id != $validated['project_id']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['task_id' => 'The selected task does not belong to the selected project.']);
        }

        $validated['user_id'] = auth()->id();

        $timeEntry = TimeEntry::create($validated);

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Time entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeEntry $timeEntry)
    {
        $timeEntry->load('user', 'project', 'task');
        return view('time-entries.show', compact('timeEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeEntry $timeEntry)
    {
        if ($timeEntry->isLocked()) {
            return redirect()->route('time-entries.show', $timeEntry)
                ->with('error', 'Cannot edit locked time entry.');
        }

        $projects = Project::active()->get();
        $tasks = Task::all();
        
        return view('time-entries.edit', compact('timeEntry', 'projects', 'tasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeEntry $timeEntry)
    {
        if ($timeEntry->isLocked()) {
            return redirect()->route('time-entries.show', $timeEntry)
                ->with('error', 'Cannot edit locked time entry.');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'is_billable' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            // make sure task belongs to project
            $task = Task::find($validated['task_id']);
            if ($task->project_id != $validated['project_id']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['task_id' => 'The selected task does not belong to the selected project.']);
            }
            $timeEntry->update($validated);
            
            return redirect()->route('time-entries.show', $timeEntry)
                ->with('success', 'Time entry updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Start a new time entry (timer).
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'required|exists:tasks,id',
            'notes' => 'nullable|string',
        ]);

        // Check if user has a running timer
        $runningEntry = TimeEntry::where('user_id', auth()->id())
            ->running()
            ->first();

        if ($runningEntry) {
            return redirect()->back()
                ->with('error', 'You already have a running timer. Please stop it first.');
        }

        $validated['user_id'] = auth()->id();
        $validated['start_time'] = now();
        $validated['is_billable'] = true;

        $timeEntry = TimeEntry::create($validated);

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Timer started successfully.');
    }

    /**
     * Stop a running time entry.
     */
    public function stop(TimeEntry $timeEntry)
    {
        if (!$timeEntry->isRunning()) {
            return redirect()->back()
                ->with('error', 'This timer is not running.');
        }

        if ($timeEntry->user_id !== auth()->id()) {
            return redirect()->back()
                ->with('error', 'You can only stop your own timers.');
        }

        $timeEntry->stop();

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Timer stopped successfully.');
    }

    /**
     * Lock a time entry.
     */
    public function lock(TimeEntry $timeEntry)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only admins can lock time entries.');
        }

        $timeEntry->lock();

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Time entry locked successfully.');
    }

    /**
     * Unlock a time entry.
     */
    public function unlock(TimeEntry $timeEntry)
    {
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Only admins can unlock time entries.');
        }

        $timeEntry->unlock();

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Time entry unlocked successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeEntry $timeEntry)
    {
        if ($timeEntry->isLocked()) {
            return redirect()->route('time-entries.index')
                ->with('error', 'Cannot delete locked time entry.');
        }

        try {
            $timeEntry->delete();
            
            return redirect()->route('time-entries.index')
                ->with('success', 'Time entry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
