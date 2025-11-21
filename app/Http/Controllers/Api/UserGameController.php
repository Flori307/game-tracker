<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameController extends Controller
{

    public function index()
    {
        $userGames = UserGame::where('user_id', Auth::id())->get();
        
        return response()->json([
            'message' => 'Ваша игровая коллекция',
            'count' => $userGames->count(),
            'user_games' => $userGames
        ]);
    }

    public function show($id)
    {
        $userGame = UserGame::with('user:id,username')
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Игра не найдена в вашей коллекции',
                'error' => 'Game not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Данные игры из вашей коллекции',
            'user_game' => $userGame
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|integer',
            'game_name' => 'required|string|max:255',
            'status' => 'required|in:want_to_play,playing,completed,dropped',
            'personal_rating' => 'nullable|integer|min:1|max:10',
            'play_time' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $existingGame = UserGame::where('user_id', Auth::id())
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingGame) {
            return response()->json([
                'message' => 'Игра уже есть в вашей коллекции'
            ], 409);
        }

        $userGame = UserGame::create(array_merge($validated, [
            'user_id' => Auth::id()
        ]));

        return response()->json([
            'message' => 'Игра добавлена в коллекцию',
            'user_game' => $userGame
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userGame = UserGame::where('user_id', Auth::id())->find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Игра не найдена в вашей коллекции'
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
            'message' => 'Данные игры обновлены',
            'user_game' => $userGame
        ]);
    }


    public function destroy($id)
    {
        $userGame = UserGame::where('user_id', Auth::id())->find($id);

        if (!$userGame) {
            return response()->json([
                'message' => 'Игра не найдена в вашей коллекции'
            ], 404);
        }

        $userGame->delete();

        return response()->json([
            'message' => 'Игра удалена из коллекции'
        ]);
    }
}