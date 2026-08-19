<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutController extends Controller
{
    public function __construct(private XpService $xpService)
    {
    }

    /** POST /api/workouts/start — open a new workout for the authenticated user. */
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'sometimes|date',
        ]);

        $workout = Workout::create([
            'user_id' => $request->user()->id,
            'date'    => $validated['date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Workout started',
            'data'    => $workout->load('sets'),
        ], 201);
    }

    /** POST /api/workouts/finish — persist a completed workout and award XP. */
    public function finish(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'                            => ['required', 'date'],
            'exercises'                       => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id'         => ['required', 'integer', 'exists:exercises,id'],
            'exercises.*.sets'                => ['required', 'array', 'min:1', 'max:12'],
            'exercises.*.sets.*.set_number'   => ['required', 'integer', 'min:1', 'max:12'],
            'exercises.*.sets.*.kg'           => ['required', 'numeric', 'min:1', 'max:500'],
            'exercises.*.sets.*.reps'         => ['required', 'integer', 'min:1', 'max:100'],
            'exercises.*.sets.*.completed'    => ['required', 'accepted'],
        ]);

        $userId   = $request->user()->id;
        $xpGained = 0;

        $workoutId = DB::transaction(function () use ($validated, $userId, &$xpGained) {
            $workoutId = DB::table('workouts')->insertGetId([
                'user_id'    => $userId,
                'date'       => $validated['date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($validated['exercises'] as $exercise) {
                $multiplier = (float) DB::table('exercises')
                    ->where('id', $exercise['exercise_id'])
                    ->value('xp_multiplier') ?: 1.0;

                foreach ($exercise['sets'] as $set) {
                    DB::table('workout_sets')->insert([
                        'workout_id'   => $workoutId,
                        'exercise_id'  => $exercise['exercise_id'],
                        'set_number'   => $set['set_number'],
                        'weight'       => $set['kg'],
                        'reps'         => $set['reps'],
                        'is_completed' => true,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $xpGained += (int) round($set['kg'] * $set['reps'] * $multiplier);
                }
            }

            return $workoutId;
        });

        $user      = $request->user();
        $xpResult  = $this->xpService->awardXp($user, $xpGained);

        return response()->json([
            'message'    => 'Workout saved successfully',
            'workout_id' => $workoutId,
            'xp'         => $xpResult,
        ], 201);
    }

    /**
     * GET /api/users/{id}/workouts — history for the authenticated user.
     * Enforces that users can only view their own workout history.
     */
    public function indexForUser(Request $request, int $id): JsonResponse
    {
        if ($request->user()->id !== $id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

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
                        'name'        => $first->exercise_name,
                        'image'       => $first->exercise_image,
                        'sets'        => $exerciseSets->map(fn ($set) => [
                            'id'           => $set->id,
                            'set_number'   => $set->set_number,
                            'weight'       => $set->weight,
                            'reps'         => $set->reps,
                            'is_completed' => (bool) $set->is_completed,
                        ])->values(),
                    ];
                })
                ->values();

            return [
                'id'        => $workout->id,
                'date'      => $workout->date,
                'exercises' => $exercises,
            ];
        });

        return response()->json([
            'user_id'  => $id,
            'workouts' => $result,
        ]);
    }
}
