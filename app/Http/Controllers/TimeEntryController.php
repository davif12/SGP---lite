<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TimeEntryController extends Controller
{
    /**
     * Get time entries for a task
     */
    public function index(Request $request, Task $task)
    {
        // Check if user has access to this task
        Gate::authorize('view', $task->epic->project);

        $timeEntries = $task->timeEntries()
            ->with('user')
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'description' => $entry->description,
                    'started_at' => $entry->started_at ? $entry->started_at->format('d/m/Y H:i') : null,
                    'ended_at' => $entry->ended_at ? $entry->ended_at->format('d/m/Y H:i') : null,
                    'duration' => $entry->human_duration,
                    'duration_seconds' => $entry->duration,
                    'current_duration' => $entry->current_human_duration,
                    'current_duration_seconds' => $entry->current_duration,
                    'is_running' => $entry->is_running,
                    'user' => [
                        'id' => $entry->user->id,
                        'name' => $entry->user->name,
                    ],
                    'can_edit' => $this->canEditTimeEntry($entry),
                    'can_delete' => $this->canDeleteTimeEntry($entry),
                ];
            });

        return response()->json([
            'success' => true,
            'time_entries' => $timeEntries,
            'total_time' => TimeEntry::getTotalHumanDuration($task->timeEntries),
            'total_seconds' => TimeEntry::getTotalDuration($task->timeEntries),
        ]);
    }

    /**
     * Start time tracking
     */
    public function start(Request $request, Task $task)
    {
        Gate::authorize('view', $task->epic->project);

        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Stop any running timers for this user
        TimeEntry::where('user_id', auth()->id())
            ->where('is_running', true)
            ->get()
            ->each(function ($entry) {
                $entry->stop();
            });

        // Create new time entry
        $timeEntry = TimeEntry::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'description' => $request->description,
            'started_at' => now(),
            'is_running' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Timer iniciado com sucesso!',
            'time_entry' => [
                'id' => $timeEntry->id,
                'description' => $timeEntry->description,
                'started_at' => $timeEntry->started_at->format('d/m/Y H:i'),
                'is_running' => $timeEntry->is_running,
                'current_duration' => $timeEntry->current_human_duration,
            ]
        ]);
    }

    /**
     * Stop time tracking
     */
    public function stop(Request $request, Task $task, TimeEntry $timeEntry)
    {
        Gate::authorize('view', $task->epic->project);

        if (!$this->canEditTimeEntry($timeEntry)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para parar este timer.'
            ], 403);
        }

        if (!$timeEntry->is_running) {
            return response()->json([
                'success' => false,
                'message' => 'Este timer não está rodando.'
            ], 422);
        }

        $timeEntry->stop();

        return response()->json([
            'success' => true,
            'message' => 'Timer parado com sucesso!',
            'time_entry' => [
                'id' => $timeEntry->id,
                'description' => $timeEntry->description,
                'started_at' => $timeEntry->started_at->format('d/m/Y H:i'),
                'ended_at' => $timeEntry->ended_at->format('d/m/Y H:i'),
                'duration' => $timeEntry->human_duration,
                'is_running' => $timeEntry->is_running,
            ]
        ]);
    }

    /**
     * Create manual time entry
     */
    public function store(Request $request, Task $task)
    {
        Gate::authorize('view', $task->epic->project);

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'duration_hours' => 'required|numeric|min:0.1|max:24',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $durationSeconds = $request->duration_hours * 3600;
        $startedAt = \Carbon\Carbon::parse($request->date);

        $timeEntry = TimeEntry::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'description' => $request->description,
            'started_at' => $startedAt,
            'ended_at' => $startedAt->copy()->addSeconds($durationSeconds),
            'duration' => $durationSeconds,
            'is_running' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tempo registrado com sucesso!',
            'time_entry' => [
                'id' => $timeEntry->id,
                'description' => $timeEntry->description,
                'started_at' => $timeEntry->started_at->format('d/m/Y H:i'),
                'ended_at' => $timeEntry->ended_at->format('d/m/Y H:i'),
                'duration' => $timeEntry->human_duration,
                'is_running' => $timeEntry->is_running,
                'user' => [
                    'id' => $timeEntry->user->id,
                    'name' => $timeEntry->user->name,
                ],
            ]
        ]);
    }

    /**
     * Update time entry
     */
    public function update(Request $request, Task $task, TimeEntry $timeEntry)
    {
        Gate::authorize('view', $task->epic->project);

        if (!$this->canEditTimeEntry($timeEntry)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar este registro.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'duration_hours' => 'required|numeric|min:0.1|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Stop timer if it's running
        if ($timeEntry->is_running) {
            $timeEntry->stop();
        }

        $durationSeconds = $request->duration_hours * 3600;

        $timeEntry->update([
            'description' => $request->description,
            'duration' => $durationSeconds,
            'ended_at' => $timeEntry->started_at->copy()->addSeconds($durationSeconds),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro atualizado com sucesso!',
            'time_entry' => [
                'id' => $timeEntry->id,
                'description' => $timeEntry->description,
                'duration' => $timeEntry->human_duration,
                'duration_hours' => $timeEntry->duration_hours,
            ]
        ]);
    }

    /**
     * Delete time entry
     */
    public function destroy(Task $task, TimeEntry $timeEntry)
    {
        Gate::authorize('view', $task->epic->project);

        if (!$this->canDeleteTimeEntry($timeEntry)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir este registro.'
            ], 403);
        }

        $timeEntry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de tempo excluído com sucesso!'
        ]);
    }

    /**
     * Get user's running timer
     */
    public function running(Request $request)
    {
        $runningEntry = TimeEntry::where('user_id', auth()->id())
            ->where('is_running', true)
            ->with(['task.epic.project'])
            ->first();

        if (!$runningEntry) {
            return response()->json([
                'success' => true,
                'running_timer' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'running_timer' => [
                'id' => $runningEntry->id,
                'description' => $runningEntry->description,
                'started_at' => $runningEntry->started_at->format('d/m/Y H:i'),
                'current_duration' => $runningEntry->current_human_duration,
                'current_duration_seconds' => $runningEntry->current_duration,
                'task' => [
                    'id' => $runningEntry->task->id,
                    'title' => $runningEntry->task->title,
                    'project' => $runningEntry->task->epic->project->name,
                    'url' => route('projects.epics.tasks.show', [
                        $runningEntry->task->epic->project,
                        $runningEntry->task->epic,
                        $runningEntry->task
                    ]),
                ]
            ]
        ]);
    }

    /**
     * Get time tracking statistics
     */
    public function stats(Request $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'week'); // day, week, month

        $query = TimeEntry::where('user_id', $userId);

        switch ($period) {
            case 'day':
                $query->today();
                break;
            case 'week':
                $query->thisWeek();
                break;
            case 'month':
                $query->thisMonth();
                break;
        }

        $entries = $query->with('task.epic.project')->get();
        $totalTime = TimeEntry::getTotalDuration($entries);

        $byProject = $entries->groupBy('task.epic.project.name')
            ->map(function ($projectEntries) {
                return [
                    'total_time' => TimeEntry::getTotalHumanDuration($projectEntries),
                    'total_seconds' => TimeEntry::getTotalDuration($projectEntries),
                    'entries_count' => $projectEntries->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => [
                'period' => $period,
                'total_time' => TimeEntry::getTotalHumanDuration($entries),
                'total_seconds' => $totalTime,
                'entries_count' => $entries->count(),
                'by_project' => $byProject,
            ]
        ]);
    }

    /**
     * Check if user can edit time entry
     */
    private function canEditTimeEntry(TimeEntry $timeEntry): bool
    {
        // User can edit their own entries
        if ($timeEntry->user_id === auth()->id()) {
            return true;
        }

        // Project owner can edit any entry in their project
        return $timeEntry->task->epic->project->owner_id === auth()->id();
    }

    /**
     * Check if user can delete time entry
     */
    private function canDeleteTimeEntry(TimeEntry $timeEntry): bool
    {
        return $this->canEditTimeEntry($timeEntry);
    }
}
