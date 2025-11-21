@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4">
        @if(isset($game))
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 shadow-xl border border-gray-700">
                <!-- Заголовок и основная информация -->
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Изображение -->
                    <div class="flex-shrink-0">
                        <img src="{{ $game['background_image'] }}" 
                             alt="{{ $game['name'] }}" 
                             class="w-80 h-96 object-cover rounded-2xl shadow-lg">
                    </div>
                    
                    <!-- Информация -->
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold text-white mb-4">{{ $game['name'] }}</h1>
                        
                        <!-- Рейтинг и дата -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-gray-400">Рейтинг</p>
                                <p class="text-2xl font-bold text-yellow-400">
                                    {{ $game['rating'] ?? 'N/A' }}/5
                                    @if(isset($game['rating']))
                                        <span class="text-lg text-gray-400">({{ $game['ratings_count'] ?? 'много' }} оценок)</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-400">Дата выхода</p>
                                <p class="text-xl text-white">
                                    {{ $game['released'] ? date('d.m.Y', strtotime($game['released'])) : 'TBA' }}
                                </p>
                            </div>
                        </div>

                        <!-- Платформы -->
                        <div class="mb-6">
                            <p class="text-gray-400 mb-2">Платформы</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($game['platforms'] as $platform)
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm text-gray-300">
                                        {{ is_array($platform) ? $platform['platform']['name'] : $platform }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Жанры -->
                        <div class="mb-6">
                            <p class="text-gray-400 mb-2">Жанры</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($game['genres'] as $genre)
                                    <span class="px-3 py-1 bg-green-600 rounded-full text-sm text-white">
                                        {{ $genre['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Издатель -->
                        <div class="mb-6">
                            <p class="text-gray-400 mb-2">Издатель</p>
                            <p class="text-white">
                                @foreach($game['publishers'] ?? [] as $publisher)
                                    {{ $publisher['name'] }}@if(!$loop->last), @endif
                                @endforeach
                            </p>
                        </div>

                        <!-- Кнопки действий -->
                        @auth
                            <div class="flex flex-wrap gap-4">
                                <form action="#" method="POST" class="flex gap-4">
                                    @csrf
                                    <input type="hidden" name="game_id" value="{{ $game['id'] }}">
                                    <input type="hidden" name="game_name" value="{{ $game['name'] }}">
                                    <select name="status" class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white">
                                        <option value="want_to_play">Хочу играть</option>
                                        <option value="playing">Играю</option>
                                    </select>
                                    <button type="submit" class="bg-green-600 hover:bg-green-500 px-6 py-3 rounded-lg font-semibold transition duration-200 transform hover:scale-105">
                                        Добавить в коллекцию
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <p class="text-gray-300">
                                    <a href="{{ route('login') }}" class="text-green-400 hover:text-green-300">Войдите</a> 
                                    или 
                                    <a href="{{ route('register') }}" class="text-green-400 hover:text-green-300">зарегистрируйтесь</a> 
                                    чтобы добавить игру в коллекцию
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Описание -->
                @if(isset($game['description_raw']))
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-white mb-4">Об игре</h2>
                        <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ $game['description_raw'] }}</p>
                    </div>
                @endif

                <!-- Дополнительная информация -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    <!-- Информация -->
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-4">Информация</h2>
                        <div class="space-y-2 text-gray-300">
                            <p><span class="text-gray-400">Метакритик:</span> 
                                {{ $game['metacritic'] ?? 'N/A' }}/100
                            </p>
                            <p><span class="text-gray-400">Добавлено:</span> 
                                {{ $game['added'] ?? 'N/A' }} пользователями
                            </p>
                            <p><span class="text-gray-400">Обновлено:</span> 
                                {{ isset($game['updated']) ? date('d.m.Y', strtotime($game['updated'])) : 'N/A' }}
                            </p>
                            <p><span class="text-gray-400">Веб-сайт:</span> 
                                @if(isset($game['website']))
                                    <a href="{{ $game['website'] }}" class="text-green-400 hover:text-green-300" target="_blank">
                                        Перейти
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-800 rounded-2xl p-12 text-center shadow-xl border border-gray-700">
                <i class="fas fa-exclamation-triangle text-6xl text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-semibold text-gray-400 mb-4">Игра не найдена</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    Извините, но запрашиваемая игра не существует или произошла ошибка при загрузке данных.
                </p>
                <a href="{{ route('games.index') }}" class="inline-block mt-4 text-green-400 hover:text-green-300 transition duration-200">
                    Вернуться к каталогу игр
                </a>
            </div>
        @endif
    </div>
</div>
@endsection