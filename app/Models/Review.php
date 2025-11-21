<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'game_id', 'game_name', 'title', 'content',
        'gameplay_rating', 'graphics_rating', 'story_rating', 
        'music_rating', 'atmosphere_rating', 'overall_rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Функция для расчета рейтинга
    public static function calculateOverallRating($gameplay, $graphics, $story, $music, $atmosphere)
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