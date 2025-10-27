<?php

namespace App\Http\Controllers;

use App\Models\Epic;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EpicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        $epics = $project->epics()->orderBy('created_at', 'desc')->get();

        return view('epics.index', compact('project', 'epics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        Gate::authorize('update', $project);

        return view('epics.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:backlog,in_progress,done',
            'priority' => 'required|in:low,medium,high',
        ]);

        $project->epics()->create($validated);

        return redirect()
            ->route('epics.index', $project)
            ->with('success', 'Épico criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Epic $epic)
    {
        Gate::authorize('view', $project);
        
        // Ensure epic belongs to project
        if ($epic->project_id !== $project->id) {
            abort(404);
        }

        return view('epics.show', compact('project', 'epic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Epic $epic)
    {
        Gate::authorize('update', $project);
        
        // Ensure epic belongs to project
        if ($epic->project_id !== $project->id) {
            abort(404);
        }

        return view('epics.edit', compact('project', 'epic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, Epic $epic)
    {
        Gate::authorize('update', $project);
        
        // Ensure epic belongs to project
        if ($epic->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:backlog,in_progress,done',
            'priority' => 'required|in:low,medium,high',
        ]);

        $epic->update($validated);

        return redirect()
            ->route('epics.index', $project)
            ->with('success', 'Épico atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Epic $epic)
    {
        Gate::authorize('update', $project);
        
        // Ensure epic belongs to project
        if ($epic->project_id !== $project->id) {
            abort(404);
        }

        $epic->delete();

        return redirect()
            ->route('epics.index', $project)
            ->with('success', 'Épico excluído com sucesso!');
    }
}
