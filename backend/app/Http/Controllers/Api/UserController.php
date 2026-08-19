<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(private XpService $xpService)
    {
    }

    /** GET /api/users/{id} — XP profile for a user (auth required). */
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

    /** POST /api/login — verify credentials, return Sanctum token. */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('gainzscore-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->userPayload($user),
        ]);
    }

    /** POST /api/logout — revoke the current token (auth required). */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /** POST /api/register — create account, return real Sanctum token. */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:30'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'current_xp' => 0,
            'level'      => 1,
            'rank'       => 'BRONZE I',
        ]);

        $token = $user->createToken('gainzscore-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->userPayload($user),
        ], 201);
    }

    /**
     * DELETE /api/users/{id} — delete own account after password confirmation (auth required).
     * Enforces that authenticated users can only delete their own account.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Only allow deleting the authenticated user's own account.
        if ($request->user()->id !== $id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $user = User::findOrFail($id);

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid password.'], 401);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Account deleted.']);
    }

    /** GET /api/ranking — global leaderboard sorted by total XP. */
    public function ranking(): JsonResponse
    {
        $users = User::orderByRaw('(level * (level - 1) / 2 * 1000) + current_xp DESC')
                     ->select('id', 'name', 'level', 'rank', 'current_xp')
                     ->get();

        $data = $users->map(function ($user, $index) {
            $totalXpForLevel = 0;
            for ($i = 1; $i < $user->level; $i++) {
                $totalXpForLevel += $this->xpService->xpForNextLevel($i);
            }

            return [
                'pos'      => $index + 1,
                'id'       => $user->id,
                'name'     => $user->name,
                'level'    => $user->level,
                'rank'     => $user->rank,
                'total_xp' => $totalXpForLevel + $user->current_xp,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /** Shared user response shape used by login and register. */
    private function userPayload(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'level'       => $user->level,
            'rank'        => $user->rank,
            'current_xp'  => $user->current_xp,
            'xp_for_next' => $this->xpService->xpForNextLevel($user->level),
        ];
    }
}
