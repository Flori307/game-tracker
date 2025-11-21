@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent mb-4">
                Каталог игр
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Исследуйте обширную базу данных игр, добавляйте в коллекцию и делитесь впечатлениями
            </p>
        </div>

        <div class="bg-gray-800 rounded-2xl p-6 mb-8 shadow-xl border border-gray-700">
            <form method="GET" action="{{ route('games.index') }}">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search"
                               value="{{ $search ?? '' }}"
                               placeholder="Поиск игр..." 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-500 text-white rounded-lg font-semibold transition duration-200 transform hover:scale-105">
                        Найти
                    </button>
                </div>
            </form>
        </div>

        @if(count($games) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($games as $game)
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-4 hover:from-gray-700 hover:to-gray-800 transition duration-300 transform hover:scale-105 shadow-xl border border-gray-700">
                        <a href="{{ route('games.show', $game['id']) }}" class="block">
                            <div class="mb-4">
                                <img src="{{ $game['background_image'] }}" 
                                     alt="{{ $game['name'] }}" 
                                     class="w-full h-48 object-cover rounded-lg">
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-2 line-clamp-2">{{ $game['name'] }}</h3>
                            <div class="flex items-center justify-between text-sm text-gray-400">
                                <span>Рейтинг: {{ $game['rating'] ?? 'N/A' }}/5</span>
                                <span>Дата: {{ $game['released'] ? date('d.m.Y', strtotime($game['released'])) : 'TBA' }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach(array_slice($game['genres'], 0, 2) as $genre)
                                    <span class="px-2 py-1 bg-gray-700 rounded text-xs text-gray-300">{{ $genre['name'] }}</span>
                                @endforeach
                            </div>
                        </a>
                        
                        @auth
                            <div class="mt-4">
                                <form action="#" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="game_id" value="{{ $game['id'] }}">
                                    <input type="hidden" name="game_name" value="{{ $game['name'] }}">
                                    <select name="status" class="flex-1 text-sm bg-gray-700 border border-gray-600 rounded px-2 py-1 text-white">
                                        <option value="want_to_play">Хочу играть</option>
                                        <option value="playing">Играю</option>
                                    </select>
                                    <button type="submit" class="bg-green-600 hover:bg-green-500 px-3 py-1 rounded text-sm transition duration-200">
                                        +
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-800 rounded-2xl p-12 text-center shadow-xl border border-gray-700">
                <i class="fas fa-gamepad text-6xl text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-semibold text-gray-400 mb-4">
                    {{ $search ? 'Игры не найдены' : 'Популярные игры' }}
                </h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    {{ $search ? 'Попробуйте изменить поисковый запрос' : 'Здесь будут отображаться популярные игры' }}
                </p>
                @if($search)
                    <a href="{{ route('games.index') }}" class="inline-block mt-4 text-green-400 hover:text-green-300 transition duration-200">
                        Показать все игры
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection