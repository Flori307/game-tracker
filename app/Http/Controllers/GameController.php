<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->get('search', '');
        
        $games = [
            [
                'id' => 3328,
                'name' => 'The Witcher 3: Wild Hunt',
                'background_image' => 'https://images.igdb.com/igdb/image/upload/t_720p/co2r7z.jpg',
                'rating' => 4.9,
                'released' => '2015-05-19',
                'genres' => [
                    ['name' => 'RPG'],
                    ['name' => 'Adventure']
                ]
            ],
            [
                'id' => 4291,
                'name' => 'Cyberpunk 2077',
                'background_image' => 'https://images.igdb.com/igdb/image/upload/t_720p/co2r7y.jpg',
                'rating' => 4.2,
                'released' => '2020-12-10',
                'genres' => [
                    ['name' => 'RPG'],
                    ['name' => 'Sci-fi']
                ]
            ],
            [
                'id' => 5679,
                'name' => 'Red Dead Redemption 2',
                'background_image' => 'https://images.igdb.com/igdb/image/upload/t_720p/co2r7x.jpg',
                'rating' => 4.8,
                'released' => '2018-10-26',
                'genres' => [
                    ['name' => 'Action'],
                    ['name' => 'Adventure']
                ]
            ]
        ];


        if ($search) {
            $games = array_filter($games, function($game) use ($search) {
                return stripos($game['name'], $search) !== false;
            });
        }

        return view('games.index', compact('games', 'search'));
    }

    public function show($id)
    {

        $game = [
            'id' => $id,
            'name' => 'The Witcher 3: Wild Hunt',
            'background_image' => 'https://images.igdb.com/igdb/image/upload/t_720p/co2r7z.jpg',
            'rating' => 4.9,
            'released' => '2015-05-19',
            'description_raw' => 'The Witcher 3: Wild Hunt — это история о ветеране войны, Геральте из Ривии, который ищет свое место в жестоком и темном мире. Игра сочетает в себе захватывающий сюжет, открытый мир и сложные моральные выборы.',
            'platforms' => [
                ['platform' => ['name' => 'PC']],
                ['platform' => ['name' => 'PlayStation 4']],
                ['platform' => ['name' => 'Xbox One']],
                ['platform' => ['name' => 'Nintendo Switch']]
            ],
            'genres' => [
                ['name' => 'RPG'],
                ['name' => 'Adventure'],
                ['name' => 'Action']
            ],
            'publishers' => [
                ['name' => 'CD Projekt Red']
            ],
            'metacritic' => 93,
            'added' => 15000,
            'updated' => '2023-05-15',
            'website' => 'https://www.thewitcher.com'
        ];

        return view('games.show', compact('game'));
    }
}