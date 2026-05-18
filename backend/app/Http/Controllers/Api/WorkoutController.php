<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'sometimes|date',
        ]);

        $workout = Workout::create([
            'user_id' => $validated['user_id'],
            'date'    => $validated['date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Workout started',
            'data'    => $workout->load('sets'),
        ], 201);
    }
}
