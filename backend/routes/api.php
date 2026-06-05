<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Support\Facades\Route;

// ── Auth ──────────────────────────────────────────────────────────────────────
// POST /api/login   → receives { email, password }, returns { token, user }
Route::post('/login', [UserController::class, 'login']);

// ── Exercises ─────────────────────────────────────────────────────────────────
// GET /api/exercises → returns all exercises from the database
Route::get('/exercises', [ExerciseController::class, 'index']);

// ── Workouts ──────────────────────────────────────────────────────────────────
// POST /api/workouts/start → creates a workout for the authenticated user
Route::post('/workouts/start', [WorkoutController::class, 'start']);

// ── Sets ──────────────────────────────────────────────────────────────────────
// POST /api/sets/log → logs a completed set and returns the XP gained
Route::post('/sets/log', [SetController::class, 'log']);

// ── Users ─────────────────────────────────────────────────────────────────────
// GET /api/users/{id} → returns the XP profile for a specific user
Route::get('/users/{id}', [UserController::class, 'show']);

// ── Ranking ───────────────────────────────────────────────────────────────────
// GET /api/ranking → returns users sorted by total XP descending
Route::get('/ranking', [UserController::class, 'ranking']);
