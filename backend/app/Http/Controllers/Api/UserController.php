<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private XpService $xpService)
    {
    }

    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'level'       => $user->level,
                'rank'        => $user->rank,
                'current_xp'  => $user->current_xp,
                'xp_for_next' => $this->xpService->xpForNextLevel($user->level),
            ],
        ]);
    }
}
