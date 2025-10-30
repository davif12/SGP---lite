<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class SubtaskController extends Controller
{
    /**
     * Get subtasks for a parent task
     */
    public function index(Task $task)
    {
        Gate::authorize('view', $task->epic->project);

        $subtasks = $task->subtasks()
            ->with(['assignedUser'])
            ->orderBy('position')
            ->get()
            ->map(function ($subtask) {
                return [
                    'id' => $subtask->id,
                    'title' => $subtask->title,
                    'description' => $subtask->description,
                    'status' => $subtask->status,
                    'status_label' => $subtask->status_label,
                    'priority' => $subtask->priority,
                    'priority_label' => $subtask->priority_label,
                    'priority_color' => $subtask->priority_color,
                    'position' => $subtask->position,
                    'assigned_to' => $subtask->assigned_to,
                    'assigned_user' => $subtask->assignedUser ? [
                        'id' => $subtask->assignedUser->id,
                        'name' => $subtask->assignedUser->name,
                    ] : null,
                    'created_at' => $subtask->created_at->format('d/m/Y H:i'),
                    'url' => route('projects.epics.tasks.show', [
                        $subtask->epic->project,
                        $subtask->epic,
                        $subtask
                    ]),
                ];
            });

        return response()->json([
            'success' => true,
            'subtasks' => $subtasks,
            'progress' => $task->subtasks_progress,
            'summary' => $task->subtasks_summary,
        ]);
    }

    /**
     * Create a new subtask
     */
    public function store(Request $request, Task $parentTask)
    {
        Gate::authorize('update', $parentTask->epic->project);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the next position
        $nextPosition = $parentTask->subtasks()->max('position') + 1;

        $subtask = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => Task::STATUS_TODO,
            'priority' => $request->priority,
            'position' => $nextPosition,
            'epic_id' => $parentTask->epic_id,
            'parent_id' => $parentTask->id,
            'is_subtask' => true,
            'assigned_to' => $request->assigned_to,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subtask criada com sucesso!',
            'subtask' => [
                'id' => $subtask->id,
                'title' => $subtask->title,
                'description' => $subtask->description,
                'status' => $subtask->status,
                'status_label' => $subtask->status_label,
                'priority' => $subtask->priority,
                'priority_label' => $subtask->priority_label,
                'priority_color' => $subtask->priority_color,
                'position' => $subtask->position,
                'assigned_to' => $subtask->assigned_to,
                'assigned_user' => $subtask->assignedUser ? [
                    'id' => $subtask->assignedUser->id,
                    'name' => $subtask->assignedUser->name,
                ] : null,
                'created_at' => $subtask->created_at->format('d/m/Y H:i'),
                'url' => route('projects.epics.tasks.show', [
                    $subtask->epic->project,
                    $subtask->epic,
                    $subtask
                ]),
            ]
        ]);
    }

    /**
     * Update subtask
     */
    public function update(Request $request, Task $parentTask, Task $subtask)
    {
        Gate::authorize('update', $parentTask->epic->project);

        // Verify this is actually a subtask of the parent
        if ($subtask->parent_id !== $parentTask->id) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask não pertence à task pai especificada.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $subtask->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subtask atualizada com sucesso!',
            'subtask' => [
                'id' => $subtask->id,
                'title' => $subtask->title,
                'status' => $subtask->status,
                'status_label' => $subtask->status_label,
                'priority' => $subtask->priority,
                'priority_label' => $subtask->priority_label,
                'assigned_user' => $subtask->assignedUser ? [
                    'id' => $subtask->assignedUser->id,
                    'name' => $subtask->assignedUser->name,
                ] : null,
            ],
            'parent_progress' => $parentTask->fresh()->subtasks_progress,
            'parent_summary' => $parentTask->fresh()->subtasks_summary,
        ]);
    }

    /**
     * Delete subtask
     */
    public function destroy(Task $parentTask, Task $subtask)
    {
        Gate::authorize('update', $parentTask->epic->project);

        // Verify this is actually a subtask of the parent
        if ($subtask->parent_id !== $parentTask->id) {
            return response()->json([
                'success' => false,
                'message' => 'Subtask não pertence à task pai especificada.'
            ], 422);
        }

        $subtask->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subtask excluída com sucesso!',
            'parent_progress' => $parentTask->fresh()->subtasks_progress,
            'parent_summary' => $parentTask->fresh()->subtasks_summary,
        ]);
    }

    /**
     * Reorder subtasks
     */
    public function reorder(Request $request, Task $parentTask)
    {
        Gate::authorize('update', $parentTask->epic->project);

        $validator = Validator::make($request->all(), [
            'subtasks' => 'required|array',
            'subtasks.*.id' => 'required|exists:tasks,id',
            'subtasks.*.position' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->subtasks as $subtaskData) {
            Task::where('id', $subtaskData['id'])
                ->where('parent_id', $parentTask->id)
                ->update(['position' => $subtaskData['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subtasks reordenadas com sucesso!'
        ]);
    }
}
