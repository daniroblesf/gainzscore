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

    /**
     * GET /api/users/{id}
     * Returns the XP profile for a specific user.
     */
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

    /**
     * POST /api/login
     * Authenticates a user and returns their session data.
     *
     * Expected body: { "email": "...", "password": "..." }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // TODO: Replace this mock with real Laravel Sanctum authentication:
        //   1. Install Sanctum: composer require laravel/sanctum
        //   2. Publish config: php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
        //   3. Run migrations: php artisan migrate
        //   4. Add HasApiTokens trait to the User model
        //   5. Uncomment the block below and remove the mock:
        //
        //   $user = User::where('email', $request->email)->first();
        //   if (! $user || ! Hash::check($request->password, $user->password)) {
        //       return response()->json(['message' => 'Invalid credentials.'], 401);
        //   }
        //   $token = $user->createToken('gainzscore-app')->plainTextToken;
        //   return response()->json([
        //       'token' => $token,
        //       'user'  => [
        //           'id'          => $user->id,
        //           'name'        => $user->name,
        //           'level'       => $user->level,
        //           'rank'        => $user->rank,
        //           'current_xp'  => $user->current_xp,
        //           'xp_for_next' => $this->xpService->xpForNextLevel($user->level),
        //       ],
        //   ]);

        // ── Demo mock: verifies credentials against the database ───────────────
        // Looks up the user by email and checks the hashed password.
        // Returns a static token for the demo — replace with Sanctum (see TODO above).
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('gainzscore-app')->plainTextToken;

return response()->json([
    'token' => $token,
    'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'level' => $user->level,
        'rank' => $user->rank,
        'current_xp' => $user->current_xp,
        'xp_for_next' => $this->xpService->xpForNextLevel($user->level),
    ],
]);
    }
    public function logout(Request $request): JsonResponse
{
    $request->user()->currentAccessToken()?->delete();

    return response()->json([
        'message' => 'Logged out successfully.',
    ]);
}

    /**
     * POST /api/register
     * Creates a new demo user account and returns their session data.
     *
     * Expected body: { "name": "...", "email": "...", "password": "..." }
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'current_xp' => 0,
            'level' => 1,
            'rank' => 'BRONZE I',
        ]);

        return response()->json([
            'token' => 'demo-mock-token-' . $user->id,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'level' => $user->level,
                'rank' => $user->rank,
                'current_xp' => $user->current_xp,
                'xp_for_next' => $this->xpService->xpForNextLevel($user->level),
            ],
        ], 201);
    }

    /**
     * DELETE /api/users/{id}
     * Deletes a user account after password confirmation.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = User::findOrFail($id);

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid password.',
            ], 401);
        }

        $user->delete();

        return response()->json([
            'message' => 'Account deleted.',
        ]);
    }

    /**
     * GET /api/ranking
     * Returns the global leaderboard sorted by total accumulated XP.
     */
    public function ranking(): JsonResponse
    {
        // TODO: This query already reads from the real database. Once Sanctum is active,
        //       add middleware ('auth:sanctum') to the route to protect this endpoint.
        //       You may also want to paginate results for larger datasets:
        //         ->paginate(20)
        //
        // The sort formula approximates total XP: sum of XP needed for all previous levels
        // plus current_xp. Exact formula: SUM(i * 1000 for i in 1..level-1) + current_xp
        // which simplifies to: level*(level-1)/2 * 1000 + current_xp
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
}
