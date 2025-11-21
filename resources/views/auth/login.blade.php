@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-gamepad text-4xl text-green-500"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Вход в систему
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Войдите в свой аккаунт GameTracker
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300">Имя пользователя</label>
                    <input id="username" name="username" type="text" required 
                        class="mt-1 block w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                        placeholder="Введите ваше имя пользователя" value="{{ old('username') }}">
                    @error('username')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Пароль</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                           placeholder="Введите ваш пароль">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-green-500 focus:ring-green-400 border-gray-600 rounded bg-gray-700">
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            Запомнить меня
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-green-400 hover:text-green-300 transition duration-200">
                            Забыли пароль?
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 transform hover:scale-105">
                    Войти
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-400">
                    Нет аккаунта?
                    <a href="{{ route('register') }}" class="font-medium text-green-400 hover:text-green-300 transition duration-200">
                        Зарегистрируйтесь здесь
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection