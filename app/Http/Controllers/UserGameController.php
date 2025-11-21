<?php

namespace App\Http\Controllers;

use App\Models\UserGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGameController extends Controller
{

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
            return redirect()->back()->with('error', 'Игра уже есть в вашей коллекции');
        }

        UserGame::create(array_merge($validated, [
            'user_id' => Auth::id()
        ]));

        return redirect()->back()->with('success', 'Игра добавлена в коллекцию!');
    }


    public function update(Request $request, $id)
    {
        $userGame = UserGame::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:want_to_play,playing,completed,dropped',
            'personal_rating' => 'nullable|integer|min:1|max:10',
            'play_time' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $userGame->update($validated);

        return redirect()->back()->with('success', 'Данные игры обновлены!');
    }


    public function destroy($id)
    {
        $userGame = UserGame::where('user_id', Auth::id())->findOrFail($id);
        $userGame->delete();

        return redirect()->back()->with('success', 'Игра удалена из коллекции!');
    }
}