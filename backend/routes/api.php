<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/home-stats', function () {
    $totalWorkouts = DB::table('workouts')->count();
    $totalXp = DB::table('users')->sum('current_xp') ?? 0;

    return response()->json([
        'total_workouts' => $totalWorkouts,
        'total_xp' => $totalXp,
    ]);
});

Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/users/{id}', [UserController::class, 'show']);

});

Route::post('/register', [UserController::class, 'register']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/exercises', [ExerciseController::class, 'index']);
Route::post('/exercises', [ExerciseController::class, 'store']);
Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy']);

Route::post('/workouts/start', [WorkoutController::class, 'start']);
Route::post('/workouts/finish', [WorkoutController::class, 'finish']);

Route::post('/sets/log', [SetController::class, 'log']);


Route::get('/users/{id}/workouts', [WorkoutController::class, 'indexForUser']);

Route::get('/ranking', [UserController::class, 'ranking']);
