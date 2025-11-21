@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 shadow-xl border border-gray-700">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Редактирование профиля</h1>
                    <p class="text-gray-400">Обновите информацию вашего аккаунта</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Имя пользователя</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email адрес</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" class="bg-green-600 hover:bg-green-500 px-6 py-3 rounded-lg font-semibold transition duration-200 transform hover:scale-105">
                            Сохранить изменения
                        </button>
                        <a href="{{ route('profile') }}" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-lg font-semibold transition duration-200">
                            Отмена
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection