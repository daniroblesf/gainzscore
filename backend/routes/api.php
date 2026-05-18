<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::get('/exercises', [ExerciseController::class, 'index']);

Route::post('/workouts/start', [WorkoutController::class, 'start']);

Route::post('/sets/log', [SetController::class, 'log']);

Route::get('/users/{id}', [UserController::class, 'show']);
