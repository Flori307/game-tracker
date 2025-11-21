<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RawgService
{
    private $baseUrl = 'https://api.rawg.io/api';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('RAWG_API_KEY');
    }

    // Поиск игр
    public function searchGames($query, $page = 1, $pageSize = 20)
    {
        $cacheKey = "games.search.{$query}.page.{$page}.pageSize.{$pageSize}";

        return Cache::remember($cacheKey, 3600, function () use ($query, $page, $pageSize) {
            $response = Http::get("{$this->baseUrl}/games", [
                'key' => $this->apiKey,
                'search' => $query,
                'page' => $page,
                'page_size' => $pageSize,
                'ordering' => '-rating',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    // Получение информации об игре по ID
    public function getGame($id)
    {
        $cacheKey = "game.{$id}";

        return Cache::remember($cacheKey, 3600, function () use ($id) {
            $response = Http::get("{$this->baseUrl}/games/{$id}", [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    // Получение популярных игр
    public function getPopularGames($pageSize = 12)
    {
        $cacheKey = "games.popular.{$pageSize}";

        return Cache::remember($cacheKey, 3600, function () use ($pageSize) {
            $response = Http::get("{$this->baseUrl}/games", [
                'key' => $this->apiKey,
                'page_size' => $pageSize,
                'ordering' => '-added',
                'metacritic' => '80,100',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    // Получение жанров
    public function getGenres()
    {
        $cacheKey = "genres";

        return Cache::remember($cacheKey, 86400, function () {
            $response = Http::get("{$this->baseUrl}/genres", [
                'key' => $this->apiKey,
                'page_size' => 50,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    // Получение платформ
    public function getPlatforms()
    {
        $cacheKey = "platforms";

        return Cache::remember($cacheKey, 86400, function () {
            $response = Http::get("{$this->baseUrl}/platforms", [
                'key' => $this->apiKey,
                'page_size' => 50,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }
}