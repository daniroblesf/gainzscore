<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────

Route::get('/home-stats', function () {
    return response()->json([
        'total_workouts' => DB::table('workouts')->count(),
        'total_xp'       => DB::table('users')->sum('current_xp') ?? 0,
    ]);
});

Route::post('/login',    [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/ranking',   [UserController::class, 'ranking']);

// ── Authenticated (requires valid Sanctum token) ──────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [UserController::class, 'logout']);

    // Users
    Route::get('/users/{id}',         [UserController::class, 'show']);
    Route::delete('/users/{id}',      [UserController::class, 'destroy']);
    Route::get('/users/{id}/workouts',[WorkoutController::class, 'indexForUser']);

    // Exercises (mutation)
    Route::post('/exercises',             [ExerciseController::class, 'store']);
    Route::delete('/exercises/{exercise}',[ExerciseController::class, 'destroy']);

    // Workouts
    Route::post('/workouts/start',  [WorkoutController::class, 'start']);
    Route::post('/workouts/finish', [WorkoutController::class, 'finish']);

    // Sets
    Route::post('/sets/log', [SetController::class, 'log']);
});
