<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @bodyParam name string required Nama pengguna. Contoh: Kumara
     * @bodyParam email string required Email pengguna. Contoh: kumara@example.com
     * @bodyParam password string required Password minimal 8 karakter. Contoh: rahasiabanget
     * @bodyParam password_confirmation string required Konfirmasi password. Contoh: rahasiabanget
     *
     * @response 201 {
     *   "user": {
     *     "id": 1,
     *     "name": "Kumara",
     *     "email": "kumara@example.com",
     *     "created_at": "2025-04-21T00:00:00.000000Z",
     *     "updated_at": "2025-04-21T00:00:00.000000Z"
     *   },
     *   "token": "1|abcdef123456..."
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login and return API token.
     *
     * @bodyParam email string required Email pengguna. Contoh: kumara@example.com
     * @bodyParam password string required Password pengguna. Contoh: rahasiabanget
     *
     * @response 200 {
     *   "token": "1|abcdef123456..."
     * }
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Logout (revoke current token).
     *
     * @authenticated
     * @response 200 {
     *   "message": "Logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}