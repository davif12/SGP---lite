<?php

namespace App\Http\Controllers;

use App\Models\Epic;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Global search across projects, epics, and tasks
     */
    public function global(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'results' => [],
                'total' => 0,
                'query' => $query
            ]);
        }

        $results = [];
        $total = 0;

        // Get user's accessible projects
        $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');

        if ($type === 'all' || $type === 'projects') {
            $projects = Project::whereIn('id', $userProjects)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->limit($limit)
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'type' => 'project',
                        'title' => $project->name,
                        'description' => $project->description,
                        'url' => route('projects.show', $project),
                        'icon' => 'bi-folder',
                        'color' => 'primary',
                        'metadata' => [
                            'owner' => $project->owner->name,
                            'members_count' => $project->users()->count(),
                            'created_at' => $project->created_at->format('d/m/Y')
                        ]
                    ];
                });

            $results = array_merge($results, $projects->toArray());
            $total += $projects->count();
        }

        if ($type === 'all' || $type === 'epics') {
            $epics = Epic::whereHas('project', function ($q) use ($userProjects) {
                    $q->whereIn('id', $userProjects);
                })
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->with('project')
                ->limit($limit)
                ->get()
                ->map(function ($epic) {
                    return [
                        'id' => $epic->id,
                        'type' => 'epic',
                        'title' => $epic->name,
                        'description' => $epic->description,
                        'url' => route('projects.epics.show', [$epic->project, $epic]),
                        'icon' => 'bi-collection',
                        'color' => 'info',
                        'metadata' => [
                            'project' => $epic->project->name,
                            'tasks_count' => $epic->tasks()->count(),
                            'created_at' => $epic->created_at->format('d/m/Y')
                        ]
                    ];
                });

            $results = array_merge($results, $epics->toArray());
            $total += $epics->count();
        }

        if ($type === 'all' || $type === 'tasks') {
            $tasks = Task::whereHas('epic.project', function ($q) use ($userProjects) {
                    $q->whereIn('id', $userProjects);
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->with(['epic.project', 'assignedUser'])
                ->limit($limit)
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'type' => 'task',
                        'title' => $task->title,
                        'description' => $task->description,
                        'url' => route('projects.epics.tasks.show', [$task->epic->project, $task->epic, $task]),
                        'icon' => 'bi-check-square',
                        'color' => $task->status_color,
                        'metadata' => [
                            'project' => $task->epic->project->name,
                            'epic' => $task->epic->name,
                            'status' => $task->status_label,
                            'priority' => $task->priority_label,
                            'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Não atribuída',
                            'created_at' => $task->created_at->format('d/m/Y')
                        ]
                    ];
                });

            $results = array_merge($results, $tasks->toArray());
            $total += $tasks->count();
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => $total,
            'query' => $query,
            'type' => $type
        ]);
    }

    /**
     * Advanced task filtering
     */
    public function tasks(Request $request)
    {
        $query = Task::query();

        // Get user's accessible projects
        $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');
        $query->whereHas('epic.project', function ($q) use ($userProjects) {
            $q->whereIn('id', $userProjects);
        });

        // Text search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->get('status')) {
            if (is_array($status)) {
                $query->whereIn('status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        // Priority filter
        if ($priority = $request->get('priority')) {
            if (is_array($priority)) {
                $query->whereIn('priority', $priority);
            } else {
                $query->where('priority', $priority);
            }
        }

        // Assigned to filter
        if ($assignedTo = $request->get('assigned_to')) {
            if ($assignedTo === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif ($assignedTo === 'me') {
                $query->where('assigned_to', auth()->id());
            } else {
                $query->where('assigned_to', $assignedTo);
            }
        }

        // Project filter
        if ($projectId = $request->get('project_id')) {
            $query->whereHas('epic.project', function ($q) use ($projectId) {
                $q->where('id', $projectId);
            });
        }

        // Epic filter
        if ($epicId = $request->get('epic_id')) {
            $query->where('epic_id', $epicId);
        }

        // Date range filter
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Due date filter
        if ($dueDateFrom = $request->get('due_date_from')) {
            $query->whereDate('due_date', '>=', $dueDateFrom);
        }

        if ($dueDateTo = $request->get('due_date_to')) {
            $query->whereDate('due_date', '<=', $dueDateTo);
        }

        // Story points range
        if ($storyPointsMin = $request->get('story_points_min')) {
            $query->where('story_points', '>=', $storyPointsMin);
        }

        if ($storyPointsMax = $request->get('story_points_max')) {
            $query->where('story_points', '<=', $storyPointsMax);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'updated_at', 'title', 'priority', 'status', 'due_date', 'story_points'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Load relationships
        $query->with(['epic.project', 'assignedUser']);

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $tasks = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'tasks' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'last_page' => $tasks->lastPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'from' => $tasks->firstItem(),
                    'to' => $tasks->lastItem(),
                ]
            ]);
        }

        return view('search.tasks', compact('tasks'));
    }

    /**
     * Get filter options for dropdowns
     */
    public function filterOptions(Request $request)
    {
        $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');

        $projects = Project::whereIn('id', $userProjects)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $epics = Epic::whereHas('project', function ($q) use ($userProjects) {
                $q->whereIn('id', $userProjects);
            })
            ->select('id', 'name', 'project_id')
            ->with('project:id,name')
            ->orderBy('name')
            ->get();

        $users = DB::table('project_user')
            ->whereIn('project_id', $userProjects)
            ->join('users', 'users.id', '=', 'project_user.user_id')
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'success' => true,
            'options' => [
                'projects' => $projects,
                'epics' => $epics,
                'users' => $users,
                'statuses' => [
                    ['value' => 'todo', 'label' => 'A Fazer'],
                    ['value' => 'in_progress', 'label' => 'Em Progresso'],
                    ['value' => 'review', 'label' => 'Em Revisão'],
                    ['value' => 'done', 'label' => 'Concluído'],
                ],
                'priorities' => [
                    ['value' => 'low', 'label' => 'Baixa'],
                    ['value' => 'medium', 'label' => 'Média'],
                    ['value' => 'high', 'label' => 'Alta'],
                    ['value' => 'critical', 'label' => 'Crítica'],
                ],
            ]
        ]);
    }
}
