<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\UserGame;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {

        $featuredReviews = Review::with('user')
            ->where('overall_rating', '>=', 80)
            ->orderBy('overall_rating', 'desc')
            ->take(3)
            ->get();


        $popularGames = Review::select('game_id', 'game_name')
            ->selectRaw('COUNT(*) as review_count, AVG(overall_rating) as avg_rating')
            ->groupBy('game_id', 'game_name')
            ->orderBy('review_count', 'desc')
            ->take(6)
            ->get();


        $stats = [
            'totalGames' => UserGame::count(),
            'totalReviews' => Review::count(),
            'activeUsers' => User::count(),
        ];

        return view('home', compact('featuredReviews', 'popularGames', 'stats'));
    }
}