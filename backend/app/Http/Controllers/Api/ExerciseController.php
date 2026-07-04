<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        return response()->json(
            Exercise::query()
                ->where(function ($query) use ($validated) {
                    $query
                        ->where(function ($globalQuery) {
                            $globalQuery
                                ->whereNull('user_id')
                                ->whereRaw('LOWER(category) != ?', ['custom']);
                        })
                        ->when($validated['user_id'] ?? null, function ($userQuery, int $userId) {
                            $userQuery->orWhere('user_id', $userId);
                        });
                })
                ->orderBy('category')
                ->orderBy('name')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => [
                'required',
                'string',
                'min:2',
                'max:80',
                Rule::unique('exercises', 'name')
                    ->where(fn ($query) => $query
                        ->where('user_id', $request->integer('user_id'))
                        ->whereNull('deleted_at')),
            ],
            'category' => ['required', 'string', 'min:2', 'max:40'],
            'xp_multiplier' => ['nullable', 'numeric', 'min:0.5', 'max:2.0'],
        ]);

        $exercise = Exercise::create([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'category' => $validated['category'],
            'image' => '/GainzScore Mini-Logo.png',
            'xp_multiplier' => $validated['xp_multiplier'] ?? 1.0,
        ]);

        return response()->json($exercise, 201);
    }

    public function destroy(Request $request, Exercise $exercise): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if ((int) $exercise->user_id !== (int) $validated['user_id']) {
            return response()->json([
                'message' => 'Diese Uebung kann nicht geloescht werden.',
            ], 403);
        }

        $exercise->delete();

        return response()->json(null, 204);
    }
}
