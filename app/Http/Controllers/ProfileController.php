<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserGame;
use App\Models\Review;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        

        $userGames = UserGame::where('user_id', $user->id)->get();
        $userReviews = Review::where('user_id', $user->id)->get();
        
        $stats = [
            'total_games' => $userGames->count(),
            'total_reviews' => $userReviews->count(),
            'completed_games' => $userGames->where('status', 'completed')->count(),
            'playing_games' => $userGames->where('status', 'playing')->count(),
            'want_to_play' => $userGames->where('status', 'want_to_play')->count(),
            'total_play_time' => $userGames->sum('play_time'),
        ];


        $recentGames = $userGames->sortByDesc('created_at')->take(5);

        $recentReviews = $userReviews->sortByDesc('created_at')->take(5);

        return view('profile.show', compact('user', 'stats', 'recentGames', 'recentReviews'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
        ]);

        return redirect()->route('profile')->with('success', 'Профиль успешно обновлен!');
    }
}