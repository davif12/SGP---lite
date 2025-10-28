<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\EpicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard-simple');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.add');
    Route::delete('projects/{project}/members/{user}', [ProjectController::class, 'removeMember'])->name('projects.members.remove');
    
    // Epic routes nested under projects
    Route::resource('projects.epics', EpicController::class)->except(['index']);
    Route::get('projects/{project}/epics', [EpicController::class, 'index'])->name('epics.index');
    
    // Task routes nested under projects and epics
    Route::resource('projects.epics.tasks', TaskController::class);
    
    // API route for moving tasks (Kanban)
    Route::patch('api/tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
    
    // Comment routes
    Route::get('api/tasks/{task}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('api/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('api/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('api/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('api/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::patch('api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('api/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('api/notifications', [NotificationController::class, 'clear'])->name('notifications.clear');
    
    // Search routes
    Route::get('/search', [SearchController::class, 'tasks'])->name('search.index');
    Route::get('api/search/global', [SearchController::class, 'global'])->name('search.global');
    Route::get('api/search/tasks', [SearchController::class, 'tasks'])->name('search.tasks');
    Route::get('api/search/filter-options', [SearchController::class, 'filterOptions'])->name('search.filter-options');
    
    // Board Kanban route
    Route::get('/board/{project?}', [TaskController::class, 'board'])->name('board.index');
});

require __DIR__.'/auth.php';
