<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SimpleAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->generateToken();

        // Формируем полный URL для аватара если есть
    $avatarUrl = null;
    if ($user->avatar_url) {
        $avatarUrl = asset('storage/' . $user->avatar_url);
    }

    return response()->json([
        'message' => 'User registered successfully',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $avatarUrl, // полный URL или null
            'created_at' => $user->created_at
        ]
    ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->generateToken();

         // Формируем полный URL для аватара если есть
    $avatarUrl = null;
    if ($user->avatar_url) {
        $avatarUrl = asset('storage/' . $user->avatar_url);
    }

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $avatarUrl, // полный URL или null
            'created_at' => $user->created_at
        ]
    ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            $user->revokeToken();
        }

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        // Формируем полный URL для аватара если есть
    $avatarUrl = null;
    if ($user->avatar_url) {
        $avatarUrl = asset('storage/' . $user->avatar_url);
    }
    return response()->json([
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $avatarUrl, // полный URL или null
            'created_at' => $user->created_at
        ]
    ]);
    }
}