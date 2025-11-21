<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGamesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('user_games')->insert([
            [
                'user_id' => 1,
                'game_id' => 3328, 
                'game_name' => 'The Witcher 3: Wild Hunt',
                'status' => 'completed',
                'personal_rating' => 10,
                'notes' => 'Отличная игра, потрясающий сюжет!',
                'play_time' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'game_id' => 4291, 
                'game_name' => 'Cyberpunk 2077',
                'status' => 'playing',
                'personal_rating' => 8,
                'notes' => 'Интересный сеттинг, хорошая графика',
                'play_time' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'game_id' => 5679, 
                'game_name' => 'Red Dead Redemption 2',
                'status' => 'completed',
                'personal_rating' => 9,
                'notes' => 'Эпичная история, красивые пейзажи',
                'play_time' => 85,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'game_id' => 280, 
                'game_name' => 'The Last of Us Part I',
                'status' => 'want_to_play',
                'personal_rating' => null,
                'notes' => 'Хочу попробовать, много хороших отзывов',
                'play_time' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}