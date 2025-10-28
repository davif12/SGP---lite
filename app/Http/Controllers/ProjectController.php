<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectMemberAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $projects = $user->allProjects()->with('owner', 'users')->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
        ]);

        // Add owner as member with owner role
        $project->users()->attach(Auth::id(), ['role' => 'owner']);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load('owner', 'users');
        
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projeto excluído com sucesso!');
    }

    public function addMember(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($project->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Usuário já é membro do projeto.');
        }

        $project->users()->attach($user->id, ['role' => 'member']);

        // Send notification to the new member
        $user->notify(new ProjectMemberAdded($project, auth()->user()));

        return back()->with('success', 'Membro adicionado com sucesso!');
    }

    public function removeMember(Project $project, User $user)
    {
        $this->authorize('update', $project);

        if ($project->owner_id === $user->id) {
            return back()->with('error', 'Não é possível remover o dono do projeto.');
        }

        $project->users()->detach($user->id);

        return back()->with('success', 'Membro removido com sucesso!');
    }
}
