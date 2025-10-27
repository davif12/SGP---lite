<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EpicController;
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
    
    // Board Kanban route (placeholder for future implementation)
    Route::get('/board', function () {
        return view('board.index');
    })->name('board.index');
});

require __DIR__.'/auth.php';
