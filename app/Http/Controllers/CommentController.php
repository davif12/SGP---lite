<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Notifications\CommentAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, Task $task)
    {
        // Check if user can view the project (and thus comment on tasks)
        Gate::authorize('view', $task->epic->project);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'task_id' => $task->id,
        ]);

        $comment->load(['user', 'attachments']);

        // Notify task assignee and project members (except comment author)
        $usersToNotify = collect();
        
        // Add task assignee
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $usersToNotify->push($task->assignedUser);
        }
        
        // Add project owner if different from comment author and assignee
        if ($task->epic->project->owner_id !== auth()->id() && 
            $task->epic->project->owner_id !== $task->assigned_to) {
            $usersToNotify->push($task->epic->project->owner);
        }
        
        // Send notifications
        $usersToNotify->unique('id')->each(function ($user) use ($comment) {
            $user->notify(new CommentAdded($comment));
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'name' => $comment->user->name,
                        'avatar' => substr($comment->user->name, 0, 1),
                    ],
                    'time_ago' => $comment->time_ago,
                    'is_editable' => $comment->is_editable,
                    'is_deletable' => $comment->is_deletable,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Comentário adicionado com sucesso!');
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user can edit this comment
        if (!$comment->is_editable) {
            abort(403, 'Você não pode editar este comentário.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'is_editable' => $comment->is_editable,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Comentário atualizado com sucesso!');
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Check if user can delete this comment
        if (!$comment->is_deletable) {
            abort(403, 'Você não pode excluir este comentário.');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comentário excluído com sucesso!'
            ]);
        }

        return redirect()->back()->with('success', 'Comentário excluído com sucesso!');
    }

    /**
     * Get comments for a task (AJAX)
     */
    public function index(Task $task)
    {
        Gate::authorize('view', $task->epic->project);

        $comments = $task->comments()
            ->with(['user', 'attachments'])
            ->oldest()
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'name' => $comment->user->name,
                        'avatar' => substr($comment->user->name, 0, 1),
                    ],
                    'time_ago' => $comment->time_ago,
                    'is_editable' => $comment->is_editable,
                    'is_deletable' => $comment->is_deletable,
                    'attachments' => $comment->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'original_name' => $attachment->original_name,
                            'size' => $attachment->human_size,
                            'mime_type' => $attachment->mime_type,
                            'is_image' => $attachment->is_image,
                            'icon' => $attachment->icon,
                            'color' => $attachment->color,
                            'url' => $attachment->url,
                            'thumbnail_url' => $attachment->thumbnail_url,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }
}
