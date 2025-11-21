<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleReviewController extends Controller
{

    public function index($userId)
    {
        $reviews = Review::where('user_id', $userId)->get();
        
        return response()->json([
            'message' => 'User reviews',
            'count' => $reviews->count(),
            'reviews' => $reviews
        ]);
    }

    public function show($id)
    {
        $review = Review::with('user:id,username')->find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Review data',
            'review' => $review
        ]);
    }

    public function store(Request $request)
{

    $user = $request->user();

    $validated = $request->validate([
        'game_id' => 'required|integer',
        'game_name' => 'required|string|max:255',
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'gameplay_rating' => 'required|integer|min:1|max:10',
        'graphics_rating' => 'required|integer|min:1|max:10',
        'story_rating' => 'required|integer|min:1|max:10',
        'music_rating' => 'required|integer|min:1|max:10',
        'atmosphere_rating' => 'required|integer|min:1|max:10',
    ]);


    

    $overallRating = $this->calculateOverallRating(
        $validated['gameplay_rating'],
        $validated['graphics_rating'],
        $validated['story_rating'],
        $validated['music_rating'],
        $validated['atmosphere_rating']
    );

    $review = Review::create(array_merge($validated, [
        'user_id' => $user->id,
        'overall_rating' => $overallRating
    ]));

    return response()->json([
        'message' => 'Review created successfully',
        'review' => $review
    ], 201);
}

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $review = Review::where('user_id', $user->id)->find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Review not found or access denied'
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'gameplay_rating' => 'sometimes|integer|min:1|max:10',
            'graphics_rating' => 'sometimes|integer|min:1|max:10',
            'story_rating' => 'sometimes|integer|min:1|max:10',
            'music_rating' => 'sometimes|integer|min:1|max:10',
            'atmosphere_rating' => 'sometimes|integer|min:1|max:10',
        ]);

        if (isset($validated['gameplay_rating']) || 
            isset($validated['graphics_rating']) || 
            isset($validated['story_rating']) || 
            isset($validated['music_rating']) || 
            isset($validated['atmosphere_rating'])) {
            
            $gameplay = $validated['gameplay_rating'] ?? $review->gameplay_rating;
            $graphics = $validated['graphics_rating'] ?? $review->graphics_rating;
            $story = $validated['story_rating'] ?? $review->story_rating;
            $music = $validated['music_rating'] ?? $review->music_rating;
            $atmosphere = $validated['atmosphere_rating'] ?? $review->atmosphere_rating;

            $validated['overall_rating'] = $this->calculateOverallRating(
                $gameplay, $graphics, $story, $music, $atmosphere
            );
        }

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated',
            'review' => $review
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $review = Review::where('user_id', $user->id)->find($id);

        if (!$review) {
            return response()->json([
                'message' => 'Review not found or access denied'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted'
        ]);
    }

    private function calculateOverallRating($gameplay, $graphics, $story, $music, $atmosphere)
    {
        $baseSum = ($gameplay + $graphics + $story + $music) * 1.4;
        
        $atmosphereMultipliers = [
            1 => 1.0000, 2 => 1.0675, 3 => 1.1349, 4 => 1.2024, 5 => 1.2699,
            6 => 1.3373, 7 => 1.4048, 8 => 1.4723, 9 => 1.5397, 10 => 1.6072
        ];
        
        $atmosphereMultiplier = $atmosphereMultipliers[$atmosphere];
        $overallRating = $baseSum * $atmosphereMultiplier;
        
        return round($overallRating, 1);
    }
}