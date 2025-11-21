@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-green-400 to-blue-400 bg-clip-text text-transparent mb-4">
                Отзывы сообщества
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Читайте рецензии других игроков и делитесь своими впечатлениями от пройденных игр
            </p>
        </div>

        <div class="bg-gray-800 rounded-2xl p-6 mb-8 shadow-xl border border-gray-700">
            <div class="flex flex-wrap gap-4">
                <select class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    <option value="">Все игры</option>
                    <option value="witcher">The Witcher 3</option>
                    <option value="rdr2">Red Dead Redemption 2</option>
                </select>
                <select class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    <option value="">Любой рейтинг</option>
                    <option value="90">90+ баллов</option>
                    <option value="80">80+ баллов</option>
                    <option value="70">70+ баллов</option>
                </select>
                <select class="px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    <option value="">Сначала новые</option>
                    <option value="old">Сначала старые</option>
                    <option value="rating">По рейтингу</option>
                </select>
            </div>
        </div>

        <div class="bg-gray-800 rounded-2xl p-12 text-center shadow-xl border border-gray-700">
            <i class="fas fa-comments text-6xl text-gray-600 mb-6"></i>
            <h3 class="text-2xl font-semibold text-gray-400 mb-4">Раздел в разработке</h3>
            <p class="text-gray-500 max-w-md mx-auto">
                Здесь будут отображаться рецензии сообщества с расширенной системой оценок и возможностью комментирования.
            </p>
        </div>
    </div>
</div>
@endsection