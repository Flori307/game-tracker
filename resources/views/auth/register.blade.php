@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-gray-800 p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-gamepad text-4xl text-green-500"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Создайте аккаунт
            </h2>
            <p class="mt-2 text-center text-sm text-gray-400">
                Присоединяйтесь к сообществу геймеров
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
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
                    <label for="email" class="block text-sm font-medium text-gray-300">Email адрес</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 block w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                           placeholder="Введите ваш email">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300">Пароль</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 block w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                           placeholder="Введите пароль">
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm font-medium text-gray-300">Подтвердите пароль</label>
                    <input id="password-confirm" name="password_confirmation" type="password" required 
                           class="mt-1 block w-full px-3 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
                           placeholder="Повторите пароль">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200 transform hover:scale-105">
                    Зарегистрироваться
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-400">
                    Уже есть аккаунт?
                    <a href="{{ route('login') }}" class="font-medium text-green-400 hover:text-green-300 transition duration-200">
                        Войдите здесь
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection