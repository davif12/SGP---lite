<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdated;
use App\Models\Epic;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Epic $epic)
    {
        Gate::authorize('view', $project);

        $tasks = $epic->tasks()
            ->with(['assignedUser'])
            ->ordered()
            ->get();

        return view('tasks.index', compact('project', 'epic', 'tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project, Epic $epic)
    {
        Gate::authorize('update', $project);

        $users = $project->users()->get()->merge([$project->owner]);
        
        return view('tasks.create', compact('project', 'epic', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project, Epic $epic)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'story_points' => 'nullable|integer|min:1|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        // Get the next position for this epic
        $maxPosition = $epic->tasks()->max('position') ?? -1;
        $validated['position'] = $maxPosition + 1;
        $validated['epic_id'] = $epic->id;

        $task = Task::create($validated);

        // Send notification if task is assigned to someone
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignedUser->notify(new TaskAssigned($task, auth()->user()));
        }

        return redirect()
            ->route('projects.epics.tasks.index', [$project, $epic])
            ->with('success', 'Task criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Epic $epic, Task $task)
    {
        Gate::authorize('view', $project);

        $task->load(['assignedUser', 'comments.user']);

        return view('tasks.show', compact('project', 'epic', 'task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Epic $epic, Task $task)
    {
        Gate::authorize('update', $project);

        $users = $project->users()->get()->merge([$project->owner]);

        return view('tasks.edit', compact('project', 'epic', 'task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Epic $epic, Task $task)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'story_points' => 'nullable|integer|min:1|max:100',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()
            ->route('projects.epics.tasks.show', [$project, $epic, $task])
            ->with('success', 'Task atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Epic $epic, Task $task)
    {
        Gate::authorize('update', $project);

        $task->delete();

        return redirect()
            ->route('projects.epics.tasks.index', [$project, $epic])
            ->with('success', 'Task excluída com sucesso!');
    }

    /**
     * Move task to different status (for Kanban board)
     */
    public function move(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,review,done',
            'position' => 'required|integer|min:0',
        ]);

        // Check if user can update the project
        Gate::authorize('update', $task->epic->project);

        $oldStatus = $task->status;

        // Update task status and position
        $task->update([
            'status' => $validated['status'],
            'position' => $validated['position'],
        ]);

        // Send notification if status changed and task is assigned to someone else
        if ($oldStatus !== $validated['status'] && $task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignedUser->notify(new TaskStatusChanged($task, $oldStatus, $validated['status'], auth()->user()));
        }

        // Broadcast task update to project members
        if ($oldStatus !== $validated['status']) {
            broadcast(new TaskUpdated($task, auth()->user(), [
                'status' => ['from' => $oldStatus, 'to' => $validated['status']]
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Task movida com sucesso!',
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
                'position' => $task->position,
            ]
        ]);
    }

    /**
     * Display Kanban board
     */
    public function board(Project $project = null)
    {
        // If no project specified, get user's projects
        if (!$project) {
            $projects = auth()->user()->projects()
                ->orWhereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })
                ->with(['epics.tasks.assignedUser'])
                ->get();
                
            return view('board.index', compact('projects'));
        }

        // Check authorization for specific project
        Gate::authorize('view', $project);

        // Get tasks for the project grouped by status
        $tasks = Task::whereHas('epic', function($query) use ($project) {
            $query->where('project_id', $project->id);
        })
        ->with(['epic', 'assignedUser'])
        ->ordered()
        ->get()
        ->groupBy('status');

        return view('board.project', compact('project', 'tasks'));
    }
}
