<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'username' => 'gamer1',
                'email' => 'gamer1@example.com',
                'password' => Hash::make('password123'),
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'pro_gamer',
                'email' => 'pro@example.com',
                'password' => Hash::make('password123'),
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'game_lover',
                'email' => 'lover@example.com',
                'password' => Hash::make('password123'),
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}