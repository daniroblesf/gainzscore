<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ── Home stats ────────────────────────────────────────────────────────────────
// GET /api/home-stats → returns total workout count and total XP across all users
Route::get('/home-stats', function () {
    $totalWorkouts = DB::table('workouts')->count();
    $totalXp       = DB::table('users')->sum('current_xp') ?? 0;

    return response()->json([
        'total_workouts' => $totalWorkouts,
        'total_xp'       => $totalXp,
    ]);
});

// ── Auth ──────────────────────────────────────────────────────────────────────
// POST /api/login → receives { email, password }, returns { token, user }
Route::post('/login', [UserController::class, 'login']);

// ── Exercises ─────────────────────────────────────────────────────────────────
// GET /api/exercises → returns all exercises from the database
Route::get('/exercises', [ExerciseController::class, 'index']);

// ── Workouts ──────────────────────────────────────────────────────────────────
// POST /api/workouts/start → creates a workout for the authenticated user
Route::post('/workouts/start', [WorkoutController::class, 'start']);

// POST /api/workouts/finish → saves a full workout with exercises and sets
Route::post('/workouts/finish', function (Request $request) {
    $validated = $request->validate([
        'user_id' => ['required', 'integer', 'exists:users,id'],
        'date' => ['required', 'date'],

        'exercises' => ['required', 'array', 'min:1'],
        'exercises.*.exercise_id' => ['required', 'integer', 'exists:exercises,id'],

        'exercises.*.sets' => ['required', 'array', 'min:1'],
        'exercises.*.sets.*.set_number' => ['required', 'integer'],
        'exercises.*.sets.*.kg' => ['required', 'numeric'],
        'exercises.*.sets.*.reps' => ['required', 'integer'],
        'exercises.*.sets.*.completed' => ['nullable', 'boolean'],
    ]);

    $workoutId = DB::table('workouts')->insertGetId([
        'user_id' => $validated['user_id'],
        'date' => $validated['date'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach ($validated['exercises'] as $exercise) {
        foreach ($exercise['sets'] as $set) {
            DB::table('workout_sets')->insert([
                'workout_id' => $workoutId,
                'exercise_id' => $exercise['exercise_id'],
                'set_number' => $set['set_number'],
                'weight' => $set['kg'],
                'reps' => $set['reps'],
                'is_completed' => $set['completed'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return response()->json([
        'message' => 'Workout saved successfully',
        'workout_id' => $workoutId,
    ], 201);
});

// ── Sets ──────────────────────────────────────────────────────────────────────
// POST /api/sets/log → logs a completed set and returns the XP gained
Route::post('/sets/log', [SetController::class, 'log']);

// ── Users ─────────────────────────────────────────────────────────────────────
// GET /api/users/{id} → returns the XP profile for a specific user
Route::get('/users/{id}', [UserController::class, 'show']);

// GET /api/users/{id}/workouts → returns all workouts with exercises and sets
Route::get('/users/{id}/workouts', function ($id) {
    $workouts = DB::table('workouts')
        ->where('user_id', $id)
        ->orderByDesc('date')
        ->orderByDesc('id')
        ->get();

    $result = $workouts->map(function ($workout) {
        $sets = DB::table('workout_sets')
            ->join('exercises', 'workout_sets.exercise_id', '=', 'exercises.id')
            ->where('workout_sets.workout_id', $workout->id)
            ->orderBy('exercises.name')
            ->orderBy('workout_sets.set_number')
            ->select(
                'workout_sets.id',
                'workout_sets.exercise_id',
                'exercises.name as exercise_name',
                'exercises.image as exercise_image',
                'workout_sets.set_number',
                'workout_sets.weight',
                'workout_sets.reps',
                'workout_sets.is_completed'
            )
            ->get();

        $exercises = $sets
            ->groupBy('exercise_id')
            ->map(function ($exerciseSets) {
                $first = $exerciseSets->first();

                return [
                    'exercise_id' => $first->exercise_id,
                    'name' => $first->exercise_name,
                    'image' => $first->exercise_image,
                    'sets' => $exerciseSets->map(function ($set) {
                        return [
                            'id' => $set->id,
                            'set_number' => $set->set_number,
                            'weight' => $set->weight,
                            'reps' => $set->reps,
                            'is_completed' => (bool) $set->is_completed,
                        ];
                    })->values(),
                ];
            })
            ->values();

        return [
            'id' => $workout->id,
            'date' => $workout->date,
            'exercises' => $exercises,
        ];
    });

    return response()->json([
        'user_id' => (int) $id,
        'workouts' => $result,
    ]);
});

// ── Ranking ───────────────────────────────────────────────────────────────────
// GET /api/ranking → returns users sorted by total XP descending
Route::get('/ranking', [UserController::class, 'ranking']);