<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $exercises = Exercise::orderBy('category')->orderBy('name')->get();

        return response()->json([
            'data' => $exercises,
        ]);
    }
}
