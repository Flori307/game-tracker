<?php

use App\Models\User;
use App\Models\Review;
use App\Models\UserGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SimpleAuthController;
use App\Http\Controllers\Api\SimpleReviewController;
use App\Http\Controllers\Api\SimpleUserGameController;

// Public routes
Route::post('/register', [SimpleAuthController::class, 'register']);
Route::post('/login', [SimpleAuthController::class, 'login']);

// Public data
Route::get('/users', function () {
    $users = User::select('id', 'username', 'email', 'created_at')->get();
    
    return response()->json([
        'message' => 'Список пользователей',
        'count' => $users->count(),
        'users' => $users
    ]);
});

Route::get('/users', function () {
    $users = User::select('id', 'username', 'email', 'avatar_url', 'created_at')->get();
    
    // Формируем полные URL для аватаров
    $usersWithFullUrls = $users->map(function($user) {
        $userArray = $user->toArray();
        if ($user->avatar_url) {
            $userArray['avatar_url'] = asset('storage/' . $user->avatar_url);
        }
        return $userArray;
    });
    
    return response()->json([
        'message' => 'Список пользователей',
        'count' => $users->count(),
        'users' => $usersWithFullUrls
    ]);
});

Route::get('/users/{id}', function ($id) {
    $user = User::select('id', 'username', 'email', 'avatar_url', 'created_at')->find($id);
    
    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }
    
    // Формируем полный URL для аватара если есть
    $avatarUrl = null;
    if ($user->avatar_url) {
        $avatarUrl = asset('storage/' . $user->avatar_url);
    }
    
    return response()->json([
        'message' => 'User data',
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $avatarUrl,
            'created_at' => $user->created_at
        ]
    ]);
});

// Public reviews
Route::get('/reviews/user/{userId}', [SimpleReviewController::class, 'index']);
Route::get('/reviews/{id}', [SimpleReviewController::class, 'show']);
Route::get('/reviews/game/{gameId}', function ($gameId) {
    $reviews = Review::with('user:id,username')
        ->where('game_id', $gameId)
        ->get();
    
    return response()->json([
        'message' => 'Reviews for game ' . $gameId,
        'count' => $reviews->count(),
        'reviews' => $reviews
    ]);
});

// Public user games
Route::get('/user-games/user/{userId}', [SimpleUserGameController::class, 'index']);
Route::get('/user-games/{id}', [SimpleUserGameController::class, 'show']);

// Protected routes (require JWT token)
Route::middleware(['auth.jwt'])->group(function () {
    
    // Auth
    Route::post('/logout', [SimpleAuthController::class, 'logout']);
    Route::get('/user', [SimpleAuthController::class, 'user']);
    
    // Reviews
    Route::post('/reviews', [SimpleReviewController::class, 'store']);
    Route::put('/reviews/{id}', [SimpleReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [SimpleReviewController::class, 'destroy']);
    Route::get('/protected/my-reviews', function (Request $request) {
        $reviews = Review::where('user_id', $request->user()->id)->get();
        
        return response()->json([
            'message' => 'Your reviews',
            'count' => $reviews->count(),
            'reviews' => $reviews
        ]);
    });
    
    // User Games
    Route::post('/user-games', [SimpleUserGameController::class, 'store']);
    Route::put('/user-games/{id}', [SimpleUserGameController::class, 'update']);
    Route::delete('/user-games/{id}', [SimpleUserGameController::class, 'destroy']);
    
    // User stats
    Route::get('/protected/stats', function (Request $request) {
        $user = $request->user();
        
        $games = UserGame::where('user_id', $user->id)->get();
        $reviews = Review::where('user_id', $user->id)->get();

        $stats = [
            'total_games' => $games->count(),
            'total_reviews' => $reviews->count(),
            'completed_games' => $games->where('status', 'completed')->count(),
            'playing_games' => $games->where('status', 'playing')->count(),
            'want_to_play' => $games->where('status', 'want_to_play')->count(),
            'total_play_time' => $games->sum('play_time'),
            'average_game_rating' => $games->avg('personal_rating'),
            'average_review_rating' => $reviews->avg('overall_rating'),
        ];

        return response()->json([
            'message' => 'User statistics',
            'user_id' => $user->id,
            'username' => $user->username,
            'stats' => $stats
        ]);
    });
});

// Debug routes
Route::get('/debug/user-games', function () {
    $userGames = UserGame::with('user:id,username')->get();
    
    return response()->json([
        'message' => 'ВСЕ игры в базе данных',
        'total_count' => $userGames->count(),
        'user_games' => $userGames
    ]);
});

Route::post('/debug/create-test-game', function (Request $request) {
    $userGame = UserGame::create([
        'user_id' => $request->user_id,
        'game_id' => $request->game_id ?? 9999,
        'game_name' => $request->game_name ?? 'Test Game',
        'status' => $request->status ?? 'playing',
        'personal_rating' => $request->personal_rating ?? 9,
        'play_time' => $request->play_time ?? 10,
    ]);
    
    return response()->json([
        'message' => 'Тестовая игра создана!',
        'user_game' => $userGame,
        'access_url' => '/api/user-games/' . $userGame->id
    ]);
});

// Avatar routes (protected)
Route::middleware(['auth.jwt'])->group(function () {
    Route::post('/user/avatar', [App\Http\Controllers\Api\AvatarController::class, 'upload']);
    Route::delete('/user/avatar', [App\Http\Controllers\Api\AvatarController::class, 'remove']);
});

// В routes/api.php добавь
Route::get('/test-avatar', function() {
    return response()->json([
        'message' => 'Avatar route works',
        'public_path' => public_path(),
        'avatars_path' => public_path('avatars'),
        'exists' => file_exists(public_path('avatars'))
    ]);
});