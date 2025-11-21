const API_BASE = 'http://ukprkfj-m4.wsr.ru/game-tracker/public/api';

class GameTrackerAPI {
    constructor() {
        this.currentPage = 1;
    }

    async fetchData(endpoint) {
        try {
            const response = await fetch(`${API_BASE}${endpoint}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Получить все отзывы
    async getReviews(page = 1) {
        return this.fetchData(`/reviews`);
    }

    // Получить всех пользователей
    async getUsers() {
        return this.fetchData('/users');
    }

    // Получить отзывы по game_id
    async getGameReviews(gameId) {
        return this.fetchData(`/reviews/game/${gameId}`);
    }

    // Получить отзывы пользователя
    async getUserReviews(userId) {
        return this.fetchData(`/reviews/user/${userId}`);
    }

    // Получить статистику пользователя
    async getUserStats(userId) {
        return this.fetchData(`/users/${userId}/stats`);
    }
}

export default new GameTrackerAPI();