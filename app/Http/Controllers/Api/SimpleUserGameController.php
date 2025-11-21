<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserGame;
use Illuminate\Http\Request;

class SimpleUserGameController extends Controller
{

    public function index($userId)
    {
        $userGames = UserGame::where('user_id', $userId)->get();
        
        return response()->json([
            'message' => 'User games collection',
            'count' => $userGames->count(),
            'user_games' => $userGames
        ]);
    }

    public function show($id)
    {
        $userGame = UserGame::find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Game data',
            'user_game' => $userGame
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'game_id' => 'required|integer',
            'game_name' => 'required|string|max:255',
            'status' => 'required|in:want_to_play,playing,completed,dropped',
            'personal_rating' => 'nullable|integer|min:1|max:10',
            'play_time' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $existingGame = UserGame::where('user_id', $validated['user_id'])
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingGame) {
            return response()->json([
                'message' => 'Game already in collection'
            ], 409);
        }

        $userGame = UserGame::create($validated);

        return response()->json([
            'message' => 'Game added to collection',
            'user_game' => $userGame
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userGame = UserGame::find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:want_to_play,playing,completed,dropped',
            'personal_rating' => 'nullable|integer|min:1|max:10',
            'play_time' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $userGame->update($validated);

        return response()->json([
            'message' => 'Game updated',
            'user_game' => $userGame
        ]);
    }

    public function destroy($id)
    {
        $userGame = UserGame::find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Game not found'
            ], 404);
        }

        $userGame->delete();

        return response()->json([
            'message' => 'Game deleted from collection'
        ]);
    }
}