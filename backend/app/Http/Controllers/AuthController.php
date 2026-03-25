<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'Username' => 'required|string|max:50|unique:users,Username',
            'Email'    => 'required|email|max:100|unique:users,Email',
            'Password' => ['required', 'string', Password::min(6)->letters()->numbers()],
        ]);

        $user = User::create([
            'Username'     => $validated['Username'],
            'Email'        => $validated['Email'],
            'PasswordHash' => Hash::make($validated['Password']),
            'IsAdmin'      => false,
            'IsActive'     => true,
        ]);

        // 1:1 user_money rekord automatikus létrehozása
        $user->money()->create(['Amount' => 0]);

        // 1:1 leaderboard rekord automatikus létrehozása
        $user->leaderboard()->create([
            'Score'          => 0,
            'LevelsCompleted'=> 0,
            'TimeTotal'      => 0,
            'HintsUsed'      => 0,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres regisztráció.',
            'user'    => $this->userResponse($user),
            'token'   => $token,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $validated = $request->validate([
            'Email'    => 'required|email',
            'Password' => 'required|string',
        ]);

        $user = User::where('Email', $validated['Email'])->first();

        if (!$user || !Hash::check($validated['Password'], $user->PasswordHash)) {
            return response()->json(['message' => 'Hibás email vagy jelszó.'], 401);
        }

        if (!$user->IsActive) {
            return response()->json(['message' => 'A fiók inaktív.'], 403);
        }

        // Régi tokenek törlése (1 aktív token policy)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés.',
            'user'    => $this->userResponse($user),
            'token'   => $token,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sikeres kijelentkezés.']);
    }

    // GET /api/me
    public function me(Request $request)
    {
        return response()->json($this->userResponse($request->user()));
    }

    // PUT /api/me/password
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password'     => ['required', 'string', Password::min(6)->letters()->numbers()],
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->PasswordHash)) {
            return response()->json(['message' => 'A jelenlegi jelszó helytelen.'], 422);
        }

        $user->PasswordHash = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Jelszó sikeresen megváltoztatva.']);
    }

    // Egységes user válasz formátum
    private function userResponse(User $user): array
    {
        return [
            'UserID'   => $user->UserID,
            'Username' => $user->Username,
            'Email'    => $user->Email,
            'IsAdmin'  => $user->IsAdmin,
            'IsActive' => $user->IsActive,
            'CreatedAt'=> $user->CreatedAt,
            'Balance'  => $user->money?->Amount ?? 0,
        ];
    }
}
