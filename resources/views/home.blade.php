@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-purple-900 via-gray-900 to-blue-900 py-20 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-6xl font-bold mb-6 bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent">
            Game Tracker
        </h1>
        <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto leading-relaxed">
            Отслеживай свою игровую коллекцию, пиши уникальные рецензии и открывай новые игры вместе с сообществом геймеров
        </p>
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="/register" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 shadow-lg">
                Начать сейчас
            </a>
            <a href="#features" class="bg-gray-700 hover:bg-gray-600 px-8 py-4 rounded-lg font-semibold text-lg transition duration-300 transform hover:scale-105 border border-gray-600">
                Узнать больше
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-800">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-gray-700 to-gray-800 p-8 rounded-2xl text-center transform hover:scale-105 transition duration-300 shadow-xl">
                <div class="text-4xl font-bold text-green-400 mb-3">{{ $stats['totalGames'] }}+</div>
                <div class="text-gray-300 text-lg">Игр в коллекциях</div>
            </div>
            <div class="bg-gradient-to-br from-gray-700 to-gray-800 p-8 rounded-2xl text-center transform hover:scale-105 transition duration-300 shadow-xl">
                <div class="text-4xl font-bold text-green-400 mb-3">{{ $stats['totalReviews'] }}+</div>
                <div class="text-gray-300 text-lg">Рецензий написано</div>
            </div>
            <div class="bg-gradient-to-br from-gray-700 to-gray-800 p-8 rounded-2xl text-center transform hover:scale-105 transition duration-300 shadow-xl">
                <div class="text-4xl font-bold text-green-400 mb-3">{{ $stats['activeUsers'] }}+</div>
                <div class="text-gray-300 text-lg">Активных пользователей</div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Games -->
<section class="py-16" id="features">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl font-bold mb-12 text-center bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
            Популярные игры
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($popularGames as $game)
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 hover:from-gray-700 hover:to-gray-800 transition duration-300 transform hover:scale-105 shadow-xl border border-gray-700">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold text-white">{{ $game->game_name }}</h3>
                    <span class="bg-gradient-to-r from-green-500 to-green-600 px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                        {{ number_format($game->avg_rating, 1) }}/90
                    </span>
                </div>
                <div class="text-gray-400 text-sm mb-4">
                    {{ $game->review_count }} отзывов
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($game->avg_rating / 90) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Reviews -->
<section class="py-16 bg-gray-800">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl font-bold mb-12 text-center bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
            Лучшие отзывы
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach($featuredReviews as $review)
            <div class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-2xl p-6 hover:from-gray-600 hover:to-gray-700 transition duration-300 transform hover:scale-105 shadow-xl border border-gray-600">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ $review->game_name }}</h3>
                        <p class="text-gray-400 text-sm">от {{ $review->user->username }}</p>
                    </div>
                    <span class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                        {{ $review->overall_rating }}/90
                    </span>
                </div>
                
                <h4 class="font-semibold text-lg mb-3 text-white">{{ $review->title }}</h4>
                <p class="text-gray-300 mb-6 leading-relaxed">{{ Str::limit($review->content, 150) }}</p>
                
                <!-- Rating Breakdown -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Геймплей:</span>
                        <div class="flex items-center">
                            <div class="w-20 bg-gray-600 rounded-full h-2 mr-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $review->gameplay_rating * 10 }}%"></div>
                            </div>
                            <span class="text-blue-400 font-semibold">{{ $review->gameplay_rating }}/10</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Графика:</span>
                        <div class="flex items-center">
                            <div class="w-20 bg-gray-600 rounded-full h-2 mr-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $review->graphics_rating * 10 }}%"></div>
                            </div>
                            <span class="text-purple-400 font-semibold">{{ $review->graphics_rating }}/10</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Сюжет:</span>
                        <div class="flex items-center">
                            <div class="w-20 bg-gray-600 rounded-full h-2 mr-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $review->story_rating * 10 }}%"></div>
                            </div>
                            <span class="text-red-400 font-semibold">{{ $review->story_rating }}/10</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Атмосфера:</span>
                        <div class="flex items-center">
                            <div class="w-20 bg-gray-600 rounded-full h-2 mr-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $review->atmosphere_rating * 10 }}%"></div>
                            </div>
                            <span class="text-green-400 font-semibold">{{ $review->atmosphere_rating }}/10</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-4xl font-bold mb-12 text-center bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
            Почему Game Tracker?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition duration-300 shadow-lg">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Отслеживание прогресса</h3>
                <p class="text-gray-400 leading-relaxed">Отмечай статус прохождения, веди учет времени и составляй персональные заметки по каждой игре</p>
            </div>
            
            <div class="text-center group">
                <div class="bg-gradient-to-br from-green-500 to-green-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition duration-300 shadow-lg">
                    <i class="fas fa-star text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Уникальная система оценок</h3>
                <p class="text-gray-400 leading-relaxed">Расширенная 90-балльная система с акцентом на атмосферу и вайб игры для более точной оценки</p>
            </div>
            
            <div class="text-center group">
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:scale-110 transition duration-300 shadow-lg">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-white">Сообщество геймеров</h3>
                <p class="text-gray-400 leading-relaxed">Делитесь опытом, находите единомышленников и открывайте новые игры через рекомендации</p>
            </div>
        </div>
    </div>
</section>
@endsection