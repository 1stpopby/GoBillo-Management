<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TimeEntryController;
use App\Http\Controllers\Api\DocumentController;

// Assets API routes
require __DIR__.'/api_assets.php';

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);

// Protected API routes
Route::middleware(['auth:sanctum', 'company.access'])->group(function () {
    
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    
    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('/projects/{project}/team', [ProjectController::class, 'team']);
    
    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/{task}/start-timer', [TaskController::class, 'startTimer']);
    Route::post('/tasks/{task}/stop-timer', [TaskController::class, 'stopTimer']);
    
    // Time Tracking
    Route::apiResource('time-entries', TimeEntryController::class);
    Route::post('/time-entries/{timeEntry}/start', [TimeEntryController::class, 'start']);
    Route::post('/time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop']);
    Route::get('/time-entries/running', [TimeEntryController::class, 'running']);
    
    // Documents
    Route::apiResource('documents', DocumentController::class);
    Route::post('/documents/upload', [DocumentController::class, 'upload']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    
    // Dashboard data
    Route::get('/dashboard/stats', function (Request $request) {
        $user = $request->user();
        
        $stats = [
            'active_projects' => \App\Models\Project::forCompany()->where('status', 'in_progress')->count(),
            'pending_tasks' => \App\Models\Task::forCompany()->where('status', 'pending')->count(),
            'my_tasks' => \App\Models\Task::forCompany()->where('assigned_to', $user->id)->whereIn('status', ['pending', 'in_progress'])->count(),
            'recent_activities' => []
        ];
        
        return response()->json($stats);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 