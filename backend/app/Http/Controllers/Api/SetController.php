<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSet;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetController extends Controller
{
    public function __construct(private XpService $xpService)
    {
    }

    public function log(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workout_id'   => 'required|exists:workouts,id',
            'exercise_id'  => 'required|exists:exercises,id',
            'set_number'   => 'required|integer|min:1',
            'weight'       => 'required|numeric|min:0',
            'reps'         => 'required|integer|min:0',
            'is_completed' => 'sometimes|boolean',
        ]);

        $set = WorkoutSet::updateOrCreate(
            [
                'workout_id'  => $validated['workout_id'],
                'exercise_id' => $validated['exercise_id'],
                'set_number'  => $validated['set_number'],
            ],
            [
                'weight'       => $validated['weight'],
                'reps'         => $validated['reps'],
                'is_completed' => $validated['is_completed'] ?? false,
            ]
        );

        $xpResult = null;

        if ($set->is_completed) {
            $set->load('exercise');
            $workout = $set->workout()->with('user')->first();
            $user = $workout->user;

            $xpGained = $this->xpService->calculateSetXp($set);
            $xpResult = $this->xpService->awardXp($user, $xpGained);
        }

        return response()->json([
            'message' => 'Set logged',
            'data'    => $set,
            'xp'      => $xpResult,
        ], 200);
    }
}
