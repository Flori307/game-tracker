const { createApp } = Vue;

createApp({
    setup() {
        const API_BASE = 'https://game-tracker-production-19a7.up.railway.app/api';
        const RAWG_API_KEY = 'f4d7726dd4f3437bb0bacb29b540b30e';
        const RAWG_API_BASE = 'https://api.rawg.io/api';

        // UI State
        const isScrolled = Vue.ref(false);
        const currentSection = Vue.ref('home');
        const showLogin = Vue.ref(false);
        const showRegister = Vue.ref(false);
        const showReviewModal = Vue.ref(false);

        // Navigation
        const navItems = [
            { id: 'home', name: 'Главная' },
            { id: 'search', name: 'Поиск' },
            { id: 'profile', name: 'Профиль' }
        ];

        // Rating Categories
        const ratingCategories = [
            { key: 'gameplay_rating', name: 'Геймплей' },
            { key: 'graphics_rating', name: 'Графика' },
            { key: 'story_rating', name: 'Сюжет' },
            { key: 'music_rating', name: 'Музыка' },
            { key: 'atmosphere_rating', name: 'Атмосфера' }
        ];

        // Auth Data
        const authToken = Vue.ref(localStorage.getItem('authToken'));
        const isAuthenticated = Vue.ref(false);
        const currentUser = Vue.ref(null);
        const authLoading = Vue.ref(false);
        const authError = Vue.ref('');

        const loginData = Vue.ref({
            username: '',
            password: ''
        });

        const registerData = Vue.ref({
            username: '',
            email: '',
            password: ''
        });

        // Review Data
        const reviewData = Vue.ref({
            title: '',
            content: '',
            gameplay_rating: 5,
            graphics_rating: 5,
            story_rating: 5,
            music_rating: 5,
            atmosphere_rating: 5
        });

        const selectedGame = Vue.ref(null);
        const reviewLoading = Vue.ref(false);
        const reviewError = Vue.ref('');

        // App Data
        const users = Vue.ref([]);
        const reviews = Vue.ref([]);
        const myReviews = Vue.ref([]);
        const userGames = Vue.ref([]);
        const searchQuery = Vue.ref('');
        const searchResults = Vue.ref([]);
        const gameReviews = Vue.ref([]);
        const recentReviews = Vue.ref([]);
        const selectedUserId = Vue.ref(null);
        const selectedUser = Vue.ref(null);
        const selectedGameName = Vue.ref('');
        
        // New data for detail pages
        const currentReview = Vue.ref(null);
        const currentGame = Vue.ref(null);
        const currentReviewGame = Vue.ref(null);
        
        const loading = Vue.ref(false);
        const searchLoading = Vue.ref(false);
        const searched = Vue.ref(false);
        const completedGames = Vue.ref(0);

        // Computed
        const totalGames = Vue.computed(() => {
            const allGames = [...reviews.value, ...gameReviews.value];
            const uniqueGames = new Set(allGames.map(review => review.game_id));
            return uniqueGames.size;
        });

        const totalReviews = Vue.computed(() => {
            return reviews.value.length + gameReviews.value.length;
        });

        const gameAverageRating = Vue.computed(() => {
            if (gameReviews.value.length === 0) return 0;
            const sum = gameReviews.value.reduce((acc, review) => acc + parseFloat(review.overall_rating), 0);
            return (sum / gameReviews.value.length).toFixed(1);
        });

        // API Client
        const apiClient = {
            async request(endpoint, options = {}) {
                const url = `${API_BASE}/${endpoint}`;
                
                const config = {
                    method: options.method || 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...options.headers
                    },
                    mode: 'cors',
                    ...options
                };
                
                if (authToken.value) {
                    config.headers['Authorization'] = `Bearer ${authToken.value}`;
                }
                
                try {
                    const response = await fetch(url, config);
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`HTTP ${response.status}: ${errorText}`);
                    }
                    
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('🔴 API Request failed:', error);
                    throw error;
                }
            },
            
            get(endpoint) {
                return this.request(endpoint);
            },
            
            post(endpoint, data) {
                return this.request(endpoint, {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
            }
        };

        // RAWG API Client
        const rawgClient = {
            async searchGames(query) {
                const url = `${RAWG_API_BASE}/games?key=${RAWG_API_KEY}&search=${encodeURIComponent(query)}&page_size=12&ordering=-rating`;
                
                try {
                    const response = await fetch(url);
                    
                    if (!response.ok) {
                        throw new Error(`RAWG API error: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    return data.results || [];
                } catch (error) {
                    console.error('🔴 RAWG API Request failed:', error);
                    throw error;
                }
            },

            async getGameDetails(gameId) {
                const url = `${RAWG_API_BASE}/games/${gameId}?key=${RAWG_API_KEY}`;
                
                try {
                    const response = await fetch(url);
                    
                    if (!response.ok) {
                        throw new Error(`RAWG API error: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('🔴 RAWG API Game details failed:', error);
                    throw error;
                }
            }
        };

        // Scroll Handler
        const handleScroll = () => {
            isScrolled.value = window.scrollY > 50;
        };

        // Auth Methods
        const checkAuth = async () => {
            if (!authToken.value) {
                isAuthenticated.value = false;
                return;
            }
            
            try {
                const data = await apiClient.get('user');
                
                isAuthenticated.value = true;
                currentUser.value = data.user;
                
                await Promise.all([
                    loadMyReviews(),
                    loadUserGames(),
                    loadRecentReviews(),
                    loadUsers()
                ]);
                
            } catch (error) {
                console.error('❌ Ошибка проверки авторизации:', error);
                
                if (error.message.includes('401') || error.message.includes('403')) {
                    logout();
                } else {
                    isAuthenticated.value = false;
                }
            }
        };

        const login = async () => {
            authLoading.value = true;
            authError.value = '';
            
            try {
                const data = await apiClient.post('login', loginData.value);

                if (data.token) {
                    authToken.value = data.token;
                    localStorage.setItem('authToken', data.token);
                    currentUser.value = data.user;
                    isAuthenticated.value = true;
                    showLogin.value = false;
                    loginData.value = { username: '', password: '' };
                    
                    await Promise.all([
                        loadMyReviews(),
                        loadUserGames(),
                        loadRecentReviews()
                    ]);
                } else {
                    authError.value = data.message || 'Ошибка входа';
                }
            } catch (error) {
                console.error('❌ Ошибка логина:', error);
                authError.value = 'Ошибка входа: ' + error.message;
            } finally {
                authLoading.value = false;
            }
        };

        const register = async () => {
            authLoading.value = true;
            authError.value = '';
            
            try {
                const data = await apiClient.post('register', registerData.value);

                if (data.token) {
                    authToken.value = data.token;
                    localStorage.setItem('authToken', data.token);
                    
                    const loginResult = await apiClient.post('login', {
                        username: registerData.value.username,
                        password: registerData.value.password
                    });
                    
                    if (loginResult.user) {
                        currentUser.value = loginResult.user;
                        isAuthenticated.value = true;
                        showRegister.value = false;
                        registerData.value = { username: '', email: '', password: '' };
                        
                        await Promise.all([
                            loadMyReviews(),
                            loadUserGames(),
                            loadRecentReviews()
                        ]);
                    }
                } else {
                    authError.value = data.message || 'Ошибка регистрации';
                }
            } catch (error) {
                console.error('❌ Ошибка регистрации:', error);
                authError.value = 'Ошибка регистрации: ' + error.message;
            } finally {
                authLoading.value = false;
            }
        };

        const logout = () => {
            authToken.value = null;
            localStorage.removeItem('authToken');
            isAuthenticated.value = false;
            currentUser.value = null;
            currentSection.value = 'home';
            myReviews.value = [];
            userGames.value = [];
            recentReviews.value = [];
        };

        // Review Methods
        const openReviewModal = (game) => {
            selectedGame.value = game;
            reviewData.value = {
                title: '',
                content: '',
                gameplay_rating: 5,
                graphics_rating: 5,
                story_rating: 5,
                music_rating: 5,
                atmosphere_rating: 5
            };
            reviewError.value = '';
            showReviewModal.value = true;
        };

        const submitReview = async () => {
            reviewLoading.value = true;
            reviewError.value = '';
            
            try {
                const reviewPayload = {
                    game_id: selectedGame.value.id,
                    game_name: selectedGame.value.name,
                    title: reviewData.value.title,
                    content: reviewData.value.content,
                    gameplay_rating: parseInt(reviewData.value.gameplay_rating),
                    graphics_rating: parseInt(reviewData.value.graphics_rating),
                    story_rating: parseInt(reviewData.value.story_rating),
                    music_rating: parseInt(reviewData.value.music_rating),
                    atmosphere_rating: parseInt(reviewData.value.atmosphere_rating)
                };

                const data = await apiClient.post('reviews', reviewPayload);
                
                showReviewModal.value = false;
                await loadMyReviews();
                await loadRecentReviews();
                
                alert('Рецензия успешно опубликована!');
                
            } catch (error) {
                console.error('❌ Ошибка создания рецензии:', error);
                reviewError.value = 'Ошибка при публикации рецензии: ' + error.message;
            } finally {
                reviewLoading.value = false;
            }
        };

        // App Methods
        const loadUsers = async () => {
            try {
                const data = await apiClient.get('users');
                users.value = data.users || [];
            } catch (error) {
                console.error('❌ Ошибка загрузки пользователей:', error);
                users.value = [];
            }
        };

        const loadMyReviews = async () => {
            if (!isAuthenticated.value) return;
            
            loading.value = true;
            
            try {
                const data = await apiClient.get('protected/my-reviews');
                myReviews.value = data.reviews || [];
                
            } catch (error) {
                console.error('❌ loadMyReviews: Ошибка загрузки:', error);
                myReviews.value = [];
            } finally {
                loading.value = false;
            }
        };

        const loadUserGames = async () => {
            if (!isAuthenticated.value) return;
            
            try {
                const data = await apiClient.get('protected/stats');
                userGames.value = data.stats.total_games || 0;
                completedGames.value = data.stats.completed_games || 0;
            } catch (error) {
                console.error('❌ Ошибка загрузки статистики:', error);
                userGames.value = 0;
                completedGames.value = 0;
            }
        };

        const selectUser = (user) => {
            selectedUserId.value = user.id;
            selectedUser.value = user;
            currentSection.value = 'reviews';
            loadUserReviews(user.id);
        };

        const loadUserReviews = async (userId) => {
            loading.value = true;
            try {
                const data = await apiClient.get(`reviews/user/${userId}`);
                reviews.value = data.reviews || [];
            } catch (error) {
                console.error('❌ Ошибка загрузки рецензий пользователя:', error);
                reviews.value = [];
            } finally {
                loading.value = false;
            }
        };

        const searchGames = async () => {
            if (!searchQuery.value.trim()) return;
            
            searchLoading.value = true;
            searched.value = true;
            try {
                const games = await rawgClient.searchGames(searchQuery.value);
                searchResults.value = games;
            } catch (error) {
                console.error('❌ Ошибка поиска через RAWG API:', error);
                searchResults.value = [];
            } finally {
                searchLoading.value = false;
            }
        };

        const viewGameReviews = async (gameId) => {
            try {
                const data = await apiClient.get(`reviews/game/${gameId}`);
                gameReviews.value = data.reviews || [];
                
                const game = searchResults.value.find(g => g.id === gameId);
                selectedGameName.value = game ? game.name : `Игра #${gameId}`;
            } catch (error) {
                console.error('❌ Ошибка загрузки рецензий на игру:', error);
                gameReviews.value = [];
            }
        };

        const loadProfileData = async () => {
            loading.value = true;
            
            try {
                const userData = await apiClient.get('user');
                currentUser.value = userData.user;

                const reviewsData = await apiClient.get('protected/my-reviews');
                myReviews.value = reviewsData.reviews || [];

                const statsData = await apiClient.get('protected/stats');
                userGames.value = statsData.stats?.total_games || 0;
                completedGames.value = statsData.stats?.completed_games || 0;
                
            } catch (error) {
                console.error('❌ Ошибка загрузки профиля:', error);
            } finally {
                loading.value = false;
            }
        };

        // New Methods for Home Page and Detail Pages
        const getPreviewText = (text) => {
            if (!text) return '';
            const preview = text.substring(0, 120);
            if (text.length > 120) {
                return preview + '...';
            }
            return preview;
        };

        const getAuthorName = (userId) => {
            const user = users.value.find(u => u.id === userId);
            return user ? user.username : 'Пользователь';
        };

        const getAuthorInitial = (userId) => {
            const name = getAuthorName(userId);
            return name.charAt(0).toUpperCase();
        };

        const toggleReviewExpand = (review) => {
            review.expanded = !review.expanded;
        };

        const openReviewPage = async (review) => {
            currentReview.value = review;
            
            try {
                const gameData = await rawgClient.getGameDetails(review.game_id);
                currentReviewGame.value = gameData;
            } catch (error) {
                console.error('Ошибка загрузки данных игры:', error);
                currentReviewGame.value = {
                    name: review.game_name,
                    background_image: 'https://via.placeholder.com/1200x400/1a1b2e/6366f1?text=No+Image',
                    released: 'Неизвестно',
                    rating: 0,
                    genres: []
                };
            }
            
            await loadGameReviews(review.game_id);
            currentSection.value = 'review-detail';
        };

        const openGamePage = async (gameId) => {
            try {
                if (typeof gameId === 'number') {
                    const gameData = await rawgClient.getGameDetails(gameId);
                    currentGame.value = gameData;
                } else {
                    currentGame.value = currentReviewGame.value;
                }
                
                await loadGameReviews(gameId);
                currentSection.value = 'game-detail';
            } catch (error) {
                console.error('Ошибка загрузки страницы игры:', error);
            }
        };

        const loadGameReviews = async (gameId) => {
            try {
                const data = await apiClient.get(`reviews/game/${gameId}`);
                gameReviews.value = data.reviews || [];
            } catch (error) {
                console.error('Ошибка загрузки рецензий игры:', error);
                gameReviews.value = [];
            }
        };

        const getGameCover = async (gameId) => {
            try {
                const gameFromSearch = searchResults.value.find(g => g.id === gameId);
                if (gameFromSearch && gameFromSearch.background_image) {
                    return gameFromSearch.background_image;
                }
                
                if (currentGame.value && currentGame.value.id === gameId && currentGame.value.background_image) {
                    return currentGame.value.background_image;
                }
                
                const gameData = await rawgClient.getGameDetails(gameId);
                if (gameData && gameData.background_image) {
                    return gameData.background_image;
                }
                
            } catch (error) {
                console.error(`❌ Ошибка загрузки обложки для игры ${gameId}:`, error);
            }
            
            const fallbackCovers = [
                'https://media.rawg.io/media/games/456/456dea5e1c7e3cd07060c14e96612001.jpg',
                'https://media.rawg.io/media/games/328/3283617cb7d75d67257fc58339188742.jpg',
                'https://media.rawg.io/media/games/618/618c2031a07bbff6b4f611f10b6bcdbc.jpg',
                'https://media.rawg.io/media/games/7cf/7cfc9220b401b7a300e409e539c9afd5.jpg'
            ];
            const randomIndex = Math.floor(Math.random() * fallbackCovers.length);
            return fallbackCovers[randomIndex];
        };

        const handleImageError = async (event, gameId) => {
            const img = event.target;
            if (!img) return;

            try {
                const newCover = await getGameCover(gameId);
                if (newCover && img) {
                    img.src = newCover;
                } else {
                    setFallbackImage(img);
                }
            } catch (error) {
                setFallbackImage(img);
            }
        };

        const setFallbackImage = (img) => {
            if (!img) return;
            
            const fallbackCovers = [
                'https://media.rawg.io/media/games/456/456dea5e1c7e3cd07060c14e96612001.jpg',
                'https://media.rawg.io/media/games/328/3283617cb7d75d67257fc58339188742.jpg',
                'https://via.placeholder.com/300x200/1a1b2e/6366f1?text=No+Image'
            ];
            const randomIndex = Math.floor(Math.random() * fallbackCovers.length);
            img.src = fallbackCovers[randomIndex];
        };

        const handleImageLoad = (event) => {
            console.log('✅ Изображение загружено успешно');
        };

        const loadRecentReviews = async () => {
            loading.value = true;
            try {
                await loadUsers();
                
                let allReviews = [];
                
                if (users.value.length > 0) {
                    for (const user of users.value) {
                        try {
                            const userReviews = await apiClient.get(`reviews/user/${user.id}`);
                            if (userReviews.reviews && userReviews.reviews.length > 0) {
                                const reviewsWithUser = userReviews.reviews.map(review => ({
                                    ...review,
                                    user_id: user.id
                                }));
                                allReviews.push(...reviewsWithUser);
                            }
                        } catch (error) {
                            console.log(`Не удалось загрузить рецензии пользователя ${user.username}`);
                        }
                        
                        if (allReviews.length >= 50) break;
                    }
                }
                
                allReviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                recentReviews.value = allReviews.slice(0, 9).map(review => ({
                    ...review,
                    expanded: false
                }));
                
            } catch (error) {
                console.error('❌ Ошибка загрузки последних рецензий:', error);
                recentReviews.value = [];
            } finally {
                loading.value = false;
            }
        };

        const formatDate = (dateString) => {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('ru-RU');
        };

        // Lifecycle
        Vue.onMounted(() => {
            window.addEventListener('scroll', handleScroll);
            checkAuth();
        });

        Vue.onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll);
        });

        // Watch за currentSection
        Vue.watch(() => currentSection.value, (newSection) => {
            if (newSection === 'profile' && isAuthenticated.value) {
                loadProfileData();
            }
        });

        return {
            // State
            isScrolled,
            currentSection,
            showLogin,
            showRegister,
            showReviewModal,
            
            // Navigation
            navItems,
            ratingCategories,
            
            // Auth
            isAuthenticated,
            currentUser,
            authLoading,
            authError,
            loginData,
            registerData,
            
            // Review
            reviewData,
            selectedGame,
            reviewLoading,
            reviewError,
            
            // Data
            users,
            reviews,
            myReviews,
            userGames,
            searchQuery,
            searchResults,
            gameReviews,
            recentReviews,
            selectedUserId,
            selectedUser,
            selectedGameName,
            currentReview,
            currentGame,
            currentReviewGame,
            loading,
            searchLoading,
            searched,
            completedGames,
            
            // Computed
            totalGames,
            totalReviews,
            gameAverageRating,
            
            // Methods
            login,
            register,
            logout,
            selectUser,
            loadUserReviews,
            searchGames,
            viewGameReviews,
            openReviewModal,
            submitReview,
            formatDate,
            getPreviewText,
            getAuthorName,
            getAuthorInitial,
            toggleReviewExpand,
            openReviewPage,
            openGamePage,
            getGameCover,
            handleImageError,
            handleImageLoad,
            loadProfileData
        };
    }
}).mount('#app');