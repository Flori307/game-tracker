@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4">

        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 shadow-xl border border-gray-700 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">

                <div class="flex-shrink-0">
                    <div class="w-32 h-32 bg-gradient-to-br from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                </div>
                
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-bold text-white mb-2">{{ $user->username }}</h1>
                    <p class="text-gray-400 text-lg mb-4">{{ $user->email }}</p>
                    <p class="text-gray-500">Участник с {{ $user->created_at->format('d.m.Y') }}</p>
                    
                    <div class="mt-6">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Редактировать профиль
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-400">{{ $stats['total_games'] }}</div>
                        <div class="text-gray-400 text-sm">Всего игр</div>
                    </div>
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-400">{{ $stats['completed_games'] }}</div>
                        <div class="text-gray-400 text-sm">Пройдено</div>
                    </div>
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-400">{{ $stats['playing_games'] }}</div>
                        <div class="text-gray-400 text-sm">В процессе</div>
                    </div>
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-400">{{ $stats['total_reviews'] }}</div>
                        <div class="text-gray-400 text-sm">Отзывов</div>
                    </div>
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-red-400">{{ $stats['want_to_play'] }}</div>
                        <div class="text-gray-400 text-sm">Хочу играть</div>
                    </div>
                    <div class="text-center bg-gray-700 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-indigo-400">{{ $stats['total_play_time'] }}</div>
                        <div class="text-gray-400 text-sm">Часов в играх</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 shadow-xl border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Последние игры</h2>
                    <a href="{{ route('games.index') }}" class="text-green-400 hover:text-green-300 text-sm transition duration-200">
                        Все игры →
                    </a>
                </div>
                
                @if($recentGames->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentGames as $game)
                        <div class="flex items-center justify-between p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition duration-200">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gamepad"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">{{ $game->game_name }}</h3>
                                    <div class="flex items-center space-x-2 mt-1">
                                        @php
                                            $statusColors = [
                                                'want_to_play' => 'bg-yellow-500',
                                                'playing' => 'bg-blue-500', 
                                                'completed' => 'bg-green-500',
                                                'dropped' => 'bg-red-500'
                                            ];
                                            $statusLabels = [
                                                'want_to_play' => 'Хочу играть',
                                                'playing' => 'В процессе',
                                                'completed' => 'Пройдено',
                                                'dropped' => 'Брошено'
                                            ];
                                        @endphp
                                        <span class="text-xs px-2 py-1 rounded-full {{ $statusColors[$game->status] }}">
                                            {{ $statusLabels[$game->status] }}
                                        </span>
                                        @if($game->personal_rating)
                                            <span class="text-xs text-yellow-400">
                                                ★ {{ $game->personal_rating }}/10
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-400">{{ $game->play_time }}ч</div>
                                <div class="text-xs text-gray-500">{{ $game->created_at->format('d.m.Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-gamepad text-4xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400 mb-2">Игры ещё не добавлены</p>
                        <a href="{{ route('games.index') }}" class="text-green-400 hover:text-green-300 transition duration-200">
                            Добавить первую игру
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 shadow-xl border border-gray-700">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Последние отзывы</h2>
                    <a href="{{ route('reviews.index') }}" class="text-green-400 hover:text-green-300 text-sm transition duration-200">
                        Все отзывы →
                    </a>
                </div>
                
                @if($recentReviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentReviews as $review)
                        <div class="p-4 bg-gray-700 rounded-lg hover:bg-gray-600 transition duration-200">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-semibold text-white text-lg">{{ $review->game_name }}</h3>
                                <span class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-2 py-1 rounded text-sm font-bold">
                                    {{ $review->overall_rating }}/90
                                </span>
                            </div>
                            
                            <h4 class="text-white font-medium mb-2">{{ $review->title }}</h4>
                            <p class="text-gray-300 text-sm mb-3 line-clamp-2">{{ Str::limit($review->content, 100) }}</p>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex space-x-4 text-xs text-gray-400">
                                    <span>Геймплей: <span class="text-blue-400">{{ $review->gameplay_rating }}/10</span></span>
                                    <span>Сюжет: <span class="text-red-400">{{ $review->story_rating }}/10</span></span>
                                    <span>Атмосфера: <span class="text-green-400">{{ $review->atmosphere_rating }}/10</span></span>
                                </div>
                                <div class="text-xs text-gray-500">{{ $review->created_at->format('d.m.Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-4xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400 mb-2">Отзывы ещё не написаны</p>
                        <a href="{{ route('reviews.index') }}" class="text-green-400 hover:text-green-300 transition duration-200">
                            Написать первый отзыв
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection