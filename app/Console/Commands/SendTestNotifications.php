<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\CommentAdded;
use App\Notifications\ProjectMemberAdded;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use Illuminate\Console\Command;

class SendTestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test notifications to a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $this->info("Sending test notifications to {$user->name}...");

        // Get some sample data
        $project = Project::first();
        $task = Task::first();
        $comment = Comment::first();

        if (!$project || !$task || !$comment) {
            $this->error("Missing sample data. Make sure you have projects, tasks, and comments in your database.");
            return 1;
        }

        // Send different types of notifications
        try {
            // 1. Project member added
            $user->notify(new ProjectMemberAdded($project, User::first()));
            $this->info("✓ ProjectMemberAdded notification sent");

            // 2. Task assigned
            $user->notify(new TaskAssigned($task, User::first()));
            $this->info("✓ TaskAssigned notification sent");

            // 3. Task status changed
            $user->notify(new TaskStatusChanged($task, 'todo', 'in_progress', User::first()));
            $this->info("✓ TaskStatusChanged notification sent");

            // 4. Comment added
            $user->notify(new CommentAdded($comment));
            $this->info("✓ CommentAdded notification sent");

            $this->info("\n🎉 All test notifications sent successfully!");
            $this->info("Check the notifications dropdown or visit /notifications to see them.");

        } catch (\Exception $e) {
            $this->error("Error sending notifications: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
