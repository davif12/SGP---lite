<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    /**
     * Display activities for a project
     */
    public function index(Request $request, Project $project = null)
    {
        if ($project) {
            Gate::authorize('view', $project);
            $query = Activity::forProject($project->id);
        } else {
            // Get activities for all accessible projects
            $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');
            $query = Activity::whereIn('project_id', $userProjects);
        }

        // Apply filters
        if ($type = $request->get('type')) {
            $query->ofType($type);
        }

        if ($userId = $request->get('user_id')) {
            $query->byCauser($userId);
        }

        if ($days = $request->get('days')) {
            $query->recent($days);
        }

        // Load relationships
        $query->with(['causer', 'subject', 'project']);

        // Order by most recent
        $query->orderBy('created_at', 'desc');

        // Paginate
        $activities = $query->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'activities' => $activities->items(),
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'per_page' => $activities->perPage(),
                    'total' => $activities->total(),
                ]
            ]);
        }

        return view('activities.index', compact('activities', 'project'));
    }

    /**
     * Get recent activities for dashboard
     */
    public function recent(Request $request)
    {
        $limit = min($request->get('limit', 10), 50);
        $days = $request->get('days', 7);

        $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');

        $activities = Activity::whereIn('project_id', $userProjects)
            ->recent($days)
            ->with(['causer', 'subject', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'icon' => $activity->icon,
                    'color' => $activity->color,
                    'causer' => [
                        'id' => $activity->causer ? $activity->causer->id : null,
                        'name' => $activity->causer ? $activity->causer->name : 'Sistema',
                    ],
                    'project' => [
                        'id' => $activity->project->id,
                        'name' => $activity->project->name,
                    ],
                    'subject_type' => class_basename($activity->subject_type),
                    'subject_name' => $this->getSubjectName($activity->subject),
                    'properties' => $activity->properties,
                    'created_at' => $activity->created_at,
                    'time_ago' => $activity->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    /**
     * Get activity statistics
     */
    public function stats(Request $request, Project $project = null)
    {
        if ($project) {
            Gate::authorize('view', $project);
            $query = Activity::forProject($project->id);
        } else {
            $userProjects = auth()->user()->accessibleProjects()->pluck('projects.id');
            $query = Activity::whereIn('project_id', $userProjects);
        }

        $days = $request->get('days', 30);
        $query->recent($days);

        $stats = [
            'total' => $query->count(),
            'by_type' => $query->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'by_day' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date'),
            'top_users' => $query->selectRaw('causer_id, COUNT(*) as count')
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->with('causer')
                ->get()
                ->map(function ($item) {
                    return [
                        'user' => $item->causer ? $item->causer->name : 'Desconhecido',
                        'count' => $item->count
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get subject name for display
     */
    private function getSubjectName($subject): string
    {
        if (!$subject) {
            return 'Item removido';
        }

        if (method_exists($subject, 'name')) {
            return $subject->name;
        }

        if (method_exists($subject, 'title')) {
            return $subject->title;
        }

        return 'Item #' . $subject->id;
    }
}
