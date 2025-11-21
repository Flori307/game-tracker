<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Tracker - Отслеживай свои игры</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">

    <nav class="bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-gamepad text-2xl text-green-500"></i>
                    <a href="{{ route('home') }}" class="text-xl font-bold">GameTracker</a>
                </div>
                
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-green-400 transition duration-200 px-3 py-2 rounded-lg hover:bg-gray-700">Главная</a>
                    <a href="{{ route('games.index') }}" class="hover:text-green-400 transition duration-200 px-3 py-2 rounded-lg hover:bg-gray-700">Игры</a>
                    <a href="{{ route('reviews.index') }}" class="hover:text-green-400 transition duration-200 px-3 py-2 rounded-lg hover:bg-gray-700">Отзывы</a>
                </div>

                <div class="flex space-x-4">
                    @auth

                        <div class="relative group">
                            <button class="flex items-center space-x-2 bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded transition duration-200">
                                <span>{{ Auth::user()->username }}</span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-gray-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                <a href="{{ route('profile') }}" class="block px-4 py-3 hover:bg-gray-600 transition duration-200 rounded-t-lg">
                                    <i class="fas fa-user mr-2"></i>Профиль
                                </a>
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                                   class="block px-4 py-3 hover:bg-gray-600 transition duration-200 rounded-b-lg">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Выйти
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @else

                        <a href="{{ route('login') }}" class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600 transition duration-200">Войти</a>
                        <a href="{{ route('register') }}" class="bg-green-600 px-4 py-2 rounded hover:bg-green-500 transition duration-200">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main>
        @yield('content')
    </main>

    <!-- Футер -->
    <footer class="bg-gray-800 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2024 GameTracker. Все права защищены.</p>
            <p class="text-gray-400 mt-2">Отслеживайте свой игровой прогресс и делитесь впечатлениями</p>
        </div>
    </footer>
</body>
</html>