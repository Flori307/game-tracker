<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        // Функция для расчета общего рейтинга (90-балльная шкала)
        function calculateOverallRating($gameplay, $graphics, $story, $music, $atmosphere)
        {
            // 1. Сумма баллов за четыре базовых критерия умножается на множитель «1,4»
            $baseSum = ($gameplay + $graphics + $story + $music) * 1.4;
            
            // 2. Множитель «Атмосфера/Вайб» от 1,00 (1 балл) до 1,6072 (10 баллов)
            $atmosphereMultipliers = [
                1 => 1.0000,
                2 => 1.0675,
                3 => 1.1349,
                4 => 1.2024,
                5 => 1.2699,
                6 => 1.3373,
                7 => 1.4048,
                8 => 1.4723,
                9 => 1.5397,
                10 => 1.6072
            ];
            
            $atmosphereMultiplier = $atmosphereMultipliers[$atmosphere];
            
            $overallRating = $baseSum * $atmosphereMultiplier;
            
            return round($overallRating, 1);
        }

        DB::table('reviews')->insert([
            [
                'user_id' => 1,
                'game_id' => 3328,
                'game_name' => 'The Witcher 3: Wild Hunt',
                'title' => 'Шедевр игровой индустрии',
                'content' => 'Потрясающая RPG с глубоким сюжетом, интересными персонажами и огромным миром для исследования.',
                'gameplay_rating' => 9,
                'graphics_rating' => 8,
                'story_rating' => 10,
                'music_rating' => 10,
                'atmosphere_rating' => 10,
                'overall_rating' => calculateOverallRating(9, 8, 10, 10, 10), 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'game_id' => 5679,
                'game_name' => 'Red Dead Redemption 2',
                'title' => 'Кинематографичный опыт',
                'content' => 'Невероятно детализированный мир и проработанные персонажи. Игра больше похожа на интерактивное кино.',
                'gameplay_rating' => 7,
                'graphics_rating' => 10,
                'story_rating' => 9,
                'music_rating' => 9,
                'atmosphere_rating' => 10,
                'overall_rating' => calculateOverallRating(7, 10, 9, 9, 10), 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'game_id' => 4291,
                'game_name' => 'Cyberpunk 2077',
                'title' => 'Неоднозначный, но интересный',
                'content' => 'Отличная атмосфера и визуал, но геймплей мог бы быть лучше.',
                'gameplay_rating' => 7,
                'graphics_rating' => 9,
                'story_rating' => 8,
                'music_rating' => 9,
                'atmosphere_rating' => 9,
                'overall_rating' => calculateOverallRating(7, 9, 8, 9, 9),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'game_id' => 280,
                'game_name' => 'The Last of Us Part I',
                'title' => 'Эмоциональное путешествие',
                'content' => 'Сильная история, которая заставляет сопереживать персонажам.',
                'gameplay_rating' => 8,
                'graphics_rating' => 9,
                'story_rating' => 10,
                'music_rating' => 9,
                'atmosphere_rating' => 10,
                'overall_rating' => calculateOverallRating(8, 9, 10, 9, 10), 
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}