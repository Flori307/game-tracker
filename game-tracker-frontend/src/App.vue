<template>
  <div id="app">
    <!-- Header -->
    <header class="header" :class="{ 'scrolled': isScrolled }">
      <div class="container">
        <div class="header-content">
          <div class="logo">
            <div class="logo-icon">🎮</div>
            <span class="logo-text">GameTracker</span>
          </div>
          
          <nav class="nav" v-if="isAuthenticated">
            <button 
              v-for="item in navItems" 
              :key="item.id"
              @click="currentSection = item.id"
              :class="['nav-item', { active: currentSection === item.id }]"
            >
              <span class="nav-icon">{{ item.icon }}</span>
              <span class="nav-text">{{ item.name }}</span>
            </button>
          </nav>

          <div class="header-actions">
            <div v-if="isAuthenticated" class="user-section">
              <button @click="currentSection = 'profile'" class="user-btn">
                <div class="user-avatar">
                  {{ currentUser?.username?.charAt(0)?.toUpperCase() }}
                </div>
                <span class="user-name">{{ currentUser?.username }}</span>
                <div class="user-dropdown">
                  <span class="dropdown-arrow">▼</span>
                </div>
              </button>
            </div>
            <div v-else class="auth-section">
              <button @click="showLogin = true" class="auth-btn login-btn">Вход</button>
              <button @click="showRegister = true" class="auth-btn register-btn">Регистрация</button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section for Guests -->
    <section v-if="!isAuthenticated" class="hero">
      <div class="hero-background">
        <div class="hero-particles"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <div class="hero-badge">🎯 ЛУЧШИЙ ТРЕКЕР ИГР</div>
          <h1 class="hero-title">
            Отслеживай свои 
            <span class="gradient-text">игровые</span>
            достижения
          </h1>
          <p class="hero-description">
            Пиши уникальные рецензии, собирай коллекцию игр и находи единомышленников 
            в самом продвинутом игровом сообществе
          </p>
          <div class="hero-actions">
            <button @click="showRegister = true" class="btn btn-primary btn-large">
              <span class="btn-icon">🚀</span>
              Начать бесплатно
            </button>
            <button @click="showLogin = true" class="btn btn-secondary btn-large">
              Уже есть аккаунт?
            </button>
          </div>
          <div class="hero-stats">
            <div class="stat">
              <div class="stat-number">{{ totalGames }}+</div>
              <div class="stat-label">Игр в коллекциях</div>
            </div>
            <div class="stat">
              <div class="stat-number">{{ totalReviews }}+</div>
              <div class="stat-label">Рецензий написано</div>
            </div>
            <div class="stat">
              <div class="stat-number">{{ users.length }}+</div>
              <div class="stat-label">Игроков в сообществе</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <main class="main" :class="{ 'authenticated': isAuthenticated }">
      <div class="container">
        <!-- Users Section -->
        <section v-if="isAuthenticated && currentSection === 'users'" class="section">
          <div class="section-header">
            <h2 class="section-title">
              <span class="title-icon">👥</span>
              Сообщество игроков
            </h2>
            <p class="section-subtitle">Найди единомышленников и открой новые игры</p>
          </div>
          
          <div class="users-grid">
            <div 
              v-for="user in users" 
              :key="user.id" 
              class="user-card"
              @click="selectUser(user)"
              :class="{ selected: selectedUserId === user.id }"
            >
              <div class="user-card-header">
                <div class="user-avatar large">
                  {{ user.username.charAt(0).toUpperCase() }}
                </div>
                <div class="user-badge" v-if="selectedUserId === user.id">
                  <div class="badge">Выбрано</div>
                </div>
              </div>
              <div class="user-card-body">
                <h3 class="user-name">{{ user.username }}</h3>
                <p class="user-email">{{ user.email }}</p>
                <div class="user-stats">
                  <div class="user-stat">
                    <span class="stat-icon">📅</span>
                    <span>С {{ formatDate(user.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Reviews Section -->
        <section v-if="isAuthenticated && currentSection === 'reviews'" class="section">
          <div class="section-header">
            <div class="header-content">
              <div class="user-info">
                <div class="user-avatar medium">
                  {{ selectedUser?.username?.charAt(0) }}
                </div>
                <div>
                  <h2 class="section-title">Рецензии {{ selectedUser?.username }}</h2>
                  <p class="section-subtitle">{{ reviews.length }} рецензий</p>
                </div>
              </div>
              <button @click="loadUserReviews(selectedUserId)" class="btn btn-icon">
                <span class="btn-icon">🔄</span>
                Обновить
              </button>
            </div>
          </div>

          <div v-if="loading" class="loading-state">
            <div class="spinner"></div>
            <p>Загружаем рецензии...</p>
          </div>

          <div v-else class="reviews-grid">
            <div 
              v-for="review in reviews" 
              :key="review.id" 
              class="review-card"
            >
              <div class="review-card-header">
                <div class="game-info">
                  <h3 class="game-title">🎮 {{ review.game_name }}</h3>
                  <div class="game-rating">
                    <div class="rating-stars">
                      <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= Math.round(review.overall_rating / 18) }">⭐</span>
                    </div>
                    <span class="rating-score">{{ review.overall_rating }}/90</span>
                  </div>
                </div>
              </div>
              
              <h4 class="review-title">{{ review.title }}</h4>
              
              <p class="review-content">
                {{ review.content }}
              </p>

              <div class="rating-breakdown">
                <div class="rating-category" v-for="category in ratingCategories" :key="category.key">
                  <div class="category-header">
                    <span class="category-name">{{ category.name }}</span>
                    <span class="category-rating">{{ review[category.key] }}/10</span>
                  </div>
                  <div class="rating-bar">
                    <div 
                      class="rating-progress" 
                      :class="category.key"
                      :style="{ width: (review[category.key] * 10) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>

              <div class="review-footer">
                <div class="review-meta">
                  <span class="meta-item">🎯 Игра #{{ review.game_id }}</span>
                  <span class="meta-item">{{ formatDate(review.created_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="!loading && reviews.length === 0" class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>Нет рецензий</h3>
            <p>Этот игрок еще не оставил ни одной рецензии</p>
          </div>
        </section>

        <!-- Search Section -->
        <section v-if="isAuthenticated && currentSection === 'search'" class="section">
          <div class="section-header">
            <h2 class="section-title">
              <span class="title-icon">🔍</span>
              Поиск игр
            </h2>
            <p class="section-subtitle">Найди свою следующую любимую игру</p>
          </div>
          
          <div class="search-section">
            <div class="search-box">
              <div class="search-icon">🎮</div>
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Введите название игры..."
                @keyup.enter="searchGames"
                class="search-input"
              >
              <button @click="searchGames" class="btn btn-primary search-btn">
                <span class="btn-icon">🔍</span>
                Найти
              </button>
            </div>

            <div v-if="searchLoading" class="loading-state">
              <div class="spinner"></div>
              <p>Ищем игры...</p>
            </div>

            <div v-else-if="searchResults.length > 0" class="search-results">
              <h3 class="results-title">Найдено {{ searchResults.length }} игр</h3>
              <div class="games-grid">
                <div 
                  v-for="game in searchResults" 
                  :key="game.id" 
                  class="game-card"
                >
                  <div class="game-image">
                    <img :src="game.background_image || '/placeholder-image.jpg'" :alt="game.name">
                    <div class="game-overlay">
                      <button @click="viewGameReviews(game.id)" class="btn btn-sm btn-white">
                        Смотреть рецензии
                      </button>
                    </div>
                  </div>
                  <div class="game-content">
                    <h3 class="game-title">{{ game.name }}</h3>
                    <div class="game-meta">
                      <div class="meta-item">
                        <span class="meta-icon">⭐</span>
                        <span>{{ game.rating || 'N/A' }}/5</span>
                      </div>
                      <div class="meta-item">
                        <span class="meta-icon">📅</span>
                        <span>{{ game.released || 'Дата неизвестна' }}</span>
                      </div>
                    </div>
                    <div class="game-genres">
                      <span v-for="genre in game.genres.slice(0, 2)" :key="genre.id" class="genre-tag">
                        {{ genre.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="searched && !searchLoading" class="empty-state">
              <div class="empty-icon">🎯</div>
              <h3>Игры не найдены</h3>
              <p>Попробуйте изменить поисковый запрос</p>
            </div>

            <!-- Game Reviews -->
            <div v-if="gameReviews.length > 0" class="game-reviews-section">
              <div class="section-header">
                <h3 class="section-title">Рецензии на "{{ selectedGameName }}"</h3>
                <button @click="gameReviews = []" class="btn btn-text">
                  <span class="btn-icon">←</span>
                  Назад к поиску
                </button>
              </div>
              <div class="reviews-grid">
                <div 
                  v-for="review in gameReviews" 
                  :key="review.id" 
                  class="review-card"
                >
                  <div class="review-card-header">
                    <div class="game-info">
                      <h3 class="game-title">🎮 {{ review.game_name }}</h3>
                      <div class="game-rating">
                        <span class="rating-score">{{ review.overall_rating }}/90</span>
                      </div>
                    </div>
                  </div>
                  <h4 class="review-title">{{ review.title }}</h4>
                  <p class="review-content">{{ review.content }}</p>
                  <div class="review-footer">
                    <div class="review-meta">
                      <span class="meta-item">👤 Игрок #{{ review.user_id }}</span>
                      <span class="meta-item">{{ formatDate(review.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Profile Section -->
        <section v-if="isAuthenticated && currentSection === 'profile'" class="section">
          <div class="profile-header">
            <div class="profile-avatar-section">
              <div class="user-avatar xlarge">
                {{ currentUser?.username?.charAt(0)?.toUpperCase() }}
              </div>
              <div class="profile-info">
                <h1 class="profile-name">{{ currentUser?.username }}</h1>
                <p class="profile-email">{{ currentUser?.email }}</p>
                <p class="profile-join-date">Участник с {{ formatDate(currentUser?.created_at) }}</p>
              </div>
            </div>
            <button @click="logout" class="btn btn-outline">
              <span class="btn-icon">🚪</span>
              Выйти
            </button>
          </div>

          <div class="profile-stats">
            <div class="stats-grid">
              <div class="stat-card primary">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                  <div class="stat-number">{{ myReviews.length }}</div>
                  <div class="stat-label">Рецензий</div>
                </div>
              </div>
              <div class="stat-card success">
                <div class="stat-icon">🎮</div>
                <div class="stat-content">
                  <div class="stat-number">{{ userGames.length }}</div>
                  <div class="stat-label">Игр в коллекции</div>
                </div>
              </div>
              <div class="stat-card warning">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                  <div class="stat-number">{{ completedGames }}</div>
                  <div class="stat-label">Пройдено игр</div>
                </div>
              </div>
              <div class="stat-card info">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                  <div class="stat-number">{{ averageRating }}</div>
                  <div class="stat-label">Средний рейтинг</div>
                </div>
              </div>
            </div>
          </div>

          <div class="profile-content">
            <div class="content-section">
              <h3 class="section-title">Мои последние рецензии</h3>
              <div v-if="myReviews.length > 0" class="reviews-list compact">
                <div 
                  v-for="review in myReviews.slice(0, 3)" 
                  :key="review.id" 
                  class="review-item compact"
                >
                  <div class="review-header">
                    <h4 class="game-title">{{ review.game_name }}</h4>
                    <div class="review-rating">{{ review.overall_rating }}/90</div>
                  </div>
                  <p class="review-excerpt">{{ review.content.substring(0, 120) }}...</p>
                  <div class="review-date">{{ formatDate(review.created_at) }}</div>
                </div>
              </div>
              <div v-else class="empty-state small">
                <div class="empty-icon">📝</div>
                <p>Рецензий пока нет</p>
                <button @click="currentSection = 'search'" class="btn btn-primary btn-sm">
                  Написать первую рецензию
                </button>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Auth Modals -->
    <div v-if="showLogin" class="modal-overlay" @click="showLogin = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Вход в аккаунт</h2>
          <button @click="showLogin = false" class="modal-close">×</button>
        </div>
        <form @submit.prevent="login" class="modal-form">
          <div class="form-group">
            <label>Имя пользователя</label>
            <input v-model="loginData.username" type="text" required class="form-input">
          </div>
          <div class="form-group">
            <label>Пароль</label>
            <input v-model="loginData.password" type="password" required class="form-input">
          </div>
          <button type="submit" :disabled="authLoading" class="btn btn-primary btn-full">
            {{ authLoading ? 'Вход...' : 'Войти' }}
          </button>
        </form>
        <p v-if="authError" class="error-message">{{ authError }}</p>
        <div class="modal-footer">
          <p>Нет аккаунта? <button @click="showRegister = true; showLogin = false" class="link-btn">Зарегистрироваться</button></p>
        </div>
      </div>
    </div>

    <div v-if="showRegister" class="modal-overlay" @click="showRegister = false">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>Создать аккаунт</h2>
          <button @click="showRegister = false" class="modal-close">×</button>
        </div>
        <form @submit.prevent="register" class="modal-form">
          <div class="form-group">
            <label>Имя пользователя</label>
            <input v-model="registerData.username" type="text" required class="form-input">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="registerData.email" type="email" required class="form-input">
          </div>
          <div class="form-group">
            <label>Пароль</label>
            <input v-model="registerData.password" type="password" required minlength="8" class="form-input">
          </div>
          <button type="submit" :disabled="authLoading" class="btn btn-primary btn-full">
            {{ authLoading ? 'Регистрация...' : 'Создать аккаунт' }}
          </button>
        </form>
        <p v-if="authError" class="error-message">{{ authError }}</p>
        <div class="modal-footer">
          <p>Уже есть аккаунт? <button @click="showLogin = true; showRegister = false" class="link-btn">Войти</button></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const API_BASE = 'http://ukprkfj-m4.wsr.ru/game-tracker/public/api'
const RAWG_API_KEY = 'f4d7726dd4f3437bb0bacb29b540b30e'
const RAWG_BASE = 'https://api.rawg.io/api'

// UI State
const isScrolled = ref(false)
const currentSection = ref('users')
const showLogin = ref(false)
const showRegister = ref(false)

// Navigation
const navItems = [
  { id: 'users', name: 'Игроки', icon: '👥' },
  { id: 'reviews', name: 'Рецензии', icon: '📝' },
  { id: 'search', name: 'Поиск', icon: '🔍' },
  { id: 'profile', name: 'Профиль', icon: '👤' }
]

// Rating Categories
const ratingCategories = [
  { key: 'gameplay_rating', name: 'Геймплей' },
  { key: 'graphics_rating', name: 'Графика' },
  { key: 'story_rating', name: 'Сюжет' },
  { key: 'music_rating', name: 'Музыка' },
  { key: 'atmosphere_rating', name: 'Атмосфера' }
]

// Auth Data
const isAuthenticated = ref(false)
const currentUser = ref(null)
const authToken = ref(localStorage.getItem('authToken'))
const authLoading = ref(false)
const authError = ref('')

const loginData = ref({
  username: '',
  password: ''
})

const registerData = ref({
  username: '',
  email: '',
  password: ''
})

// App Data
const users = ref([])
const reviews = ref([])
const myReviews = ref([])
const userGames = ref([])
const searchQuery = ref('')
const searchResults = ref([])
const gameReviews = ref([])
const selectedUserId = ref(null)
const selectedUser = ref(null)
const selectedGameName = ref('')
const loading = ref(false)
const searchLoading = ref(false)
const searched = ref(false)

// Computed
const totalGames = computed(() => {
  const allGames = [...reviews.value, ...gameReviews.value]
  const uniqueGames = new Set(allGames.map(review => review.game_id))
  return uniqueGames.size
})

const totalReviews = computed(() => {
  return reviews.value.length + gameReviews.value.length
})

const completedGames = computed(() => {
  return userGames.value.filter(game => game.status === 'completed').length
})

const averageRating = computed(() => {
  if (myReviews.value.length === 0) return 0
  const sum = myReviews.value.reduce((acc, review) => acc + review.overall_rating, 0)
  return (sum / myReviews.value.length).toFixed(1)
})

// Scroll Handler
const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

// Auth Methods
const checkAuth = async () => {
  if (!authToken.value) {
    isAuthenticated.value = false
    return
  }

  try {
    const response = await fetch(`${API_BASE}/session/check`, {
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      isAuthenticated.value = data.authenticated
      if (data.authenticated) {
        currentUser.value = data.user
        loadMyReviews()
        loadUserGames()
      }
    } else {
      logout()
    }
  } catch (error) {
    console.error('Ошибка проверки авторизации:', error)
    logout()
  }
}

const login = async () => {
  authLoading.value = true
  authError.value = ''
  
  try {
    const response = await fetch(`${API_BASE}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(loginData.value)
    })

    const data = await response.json()

    if (response.ok) {
      authToken.value = data.token
      localStorage.setItem('authToken', data.token)
      currentUser.value = data.user
      isAuthenticated.value = true
      showLogin.value = false
      loginData.value = { username: '', password: '' }
      loadMyReviews()
      loadUserGames()
    } else {
      authError.value = data.message || 'Ошибка входа'
    }
  } catch (error) {
    authError.value = 'Ошибка сети'
  } finally {
    authLoading.value = false
  }
}

const register = async () => {
  authLoading.value = true
  authError.value = ''
  
  try {
    const response = await fetch(`${API_BASE}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(registerData.value)
    })

    const data = await response.json()

    if (response.ok) {
      authToken.value = data.token
      localStorage.setItem('authToken', data.token)
      
      // Автоматический вход после регистрации
      const loginResponse = await fetch(`${API_BASE}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          username: registerData.value.username,
          password: registerData.value.password
        })
      })
      
      const loginData = await loginResponse.json()
      if (loginResponse.ok) {
        currentUser.value = loginData.user
        isAuthenticated.value = true
        showRegister.value = false
        registerData.value = { username: '', email: '', password: '' }
        loadMyReviews()
        loadUserGames()
      }
    } else {
      authError.value = data.errors ? Object.values(data.errors).flat().join(', ') : 'Ошибка регистрации'
    }
  } catch (error) {
    authError.value = 'Ошибка сети'
  } finally {
    authLoading.value = false
  }
}

const logout = () => {
  authToken.value = null
  localStorage.removeItem('authToken')
  isAuthenticated.value = false
  currentUser.value = null
  currentSection.value = 'users'
  myReviews.value = []
  userGames.value = []
}

// App Methods
const loadUsers = async () => {
  try {
    const response = await fetch(`${API_BASE}/users`)
    const data = await response.json()
    users.value = data.users || []
  } catch (error) {
    console.error('Ошибка загрузки пользователей:', error)
  }
}

const loadMyReviews = async () => {
  if (!isAuthenticated.value) return
  
  loading.value = true
  try {
    const response = await fetch(`${API_BASE}/protected/my-reviews`, {
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      myReviews.value = data.reviews || []
    }
  } catch (error) {
    console.error('Ошибка загрузки моих рецензий:', error)
    myReviews.value = []
  } finally {
    loading.value = false
  }
}

const loadUserGames = async () => {
  if (!isAuthenticated.value) return
  
  try {
    const response = await fetch(`${API_BASE}/user-games/user/${currentUser.value.id}`, {
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      userGames.value = data.user_games || []
    }
  } catch (error) {
    console.error('Ошибка загрузки коллекции игр:', error)
    userGames.value = []
  }
}

const selectUser = (user) => {
  selectedUserId.value = user.id
  selectedUser.value = user
  currentSection.value = 'reviews'
  loadUserReviews(user.id)
}

const loadUserReviews = async (userId) => {
  loading.value = true
  try {
    const response = await fetch(`${API_BASE}/reviews/user/${userId}`)
    const data = await response.json()
    reviews.value = data.reviews || []
  } catch (error) {
    console.error('Ошибка загрузки рецензий:', error)
    reviews.value = []
  } finally {
    loading.value = false
  }
}

const searchGames = async () => {
  if (!searchQuery.value.trim()) return
  
  searchLoading.value = true
  searched.value = true
  try {
    const response = await fetch(
      `${RAWG_BASE}/games?key=${RAWG_API_KEY}&search=${encodeURIComponent(searchQuery.value)}&page_size=12`
    )
    const data = await response.json()
    searchResults.value = data.results || []
  } catch (error) {
    console.error('Ошибка поиска игр:', error)
    searchResults.value = []
  } finally {
    searchLoading.value = false
  }
}

const viewGameReviews = async (gameId) => {
  try {
    const response = await fetch(`${API_BASE}/reviews/game/${gameId}`)
    const data = await response.json()
    gameReviews.value = data.reviews || []
    
    const game = searchResults.value.find(g => g.id === gameId)
    selectedGameName.value = game ? game.name : `Игра #${gameId}`
  } catch (error) {
    console.error('Ошибка загрузки рецензий:', error)
    gameReviews.value = []
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('ru-RU')
}

// Lifecycle
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  checkAuth()
  loadUsers()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style>
/* Reset and Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --primary: #6366f1;
  --primary-dark: #4f46e5;
  --secondary: #f59e0b;
  --success: #10b981;
  --warning: #f59e0b;
  --error: #ef4444;
  --background: #0f0f23;
  --surface: #1a1b2e;
  --surface-light: #2d2e42;
  --text-primary: #ffffff;
  --text-secondary: #a1a1aa;
  --text-muted: #6b7280;
  --border: #2d2e42;
  --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
  --gradient: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  background: var(--background);
  color: var(--text-primary);
  line-height: 1.6;
  overflow-x: hidden;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Header Styles */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(15, 15, 35, 0.8);
  backdrop-filter: blur(20px);
  border-bottom: 1px solid var(--border);
  z-index: 1000;
  transition: all 0.3s ease;
}

.header.scrolled {
  background: rgba(15, 15, 35, 0.95);
  border-bottom-color: var(--surface-light);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
  font-size: 1.5rem;
}

.logo-icon {
  font-size: 2rem;
}

.logo-text {
  background: var(--gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.nav {
  display: flex;
  gap: 8px;
  background: var(--surface);
  border-radius: 12px;
  padding: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  font-weight: 500;
}

.nav-item:hover {
  color: var(--text-primary);
  background: var(--surface-light);
}

.nav-item.active {
  background: var(--primary);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.auth-section {
  display: flex;
  gap: 12px;
}

.auth-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-btn {
  background: transparent;
  color: var(--text-primary);
  border: 1px solid var(--border);
}

.login-btn:hover {
  background: var(--surface-light);
}

.register-btn {
  background: var(--primary);
  color: white;
}

.register-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
  box-shadow: var(--shadow);
}

.user-section {
  display: flex;
  align-items: center;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 8px 16px;
  color: var(--text-primary);
  cursor: pointer;
  transition: all 0.3s ease;
}

.user-btn:hover {
  background: var(--surface-light);
  border-color: var(--primary);
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--gradient);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: white;
  font-size: 1rem;
}

.user-avatar.medium {
  width: 48px;
  height: 48px;
  font-size: 1.2rem;
}

.user-avatar.large {
  width: 60px;
  height: 60px;
  font-size: 1.5rem;
}

.user-avatar.xlarge {
  width: 80px;
  height: 80px;
  font-size: 2rem;
}

.user-name {
  font-weight: 500;
}

.user-dropdown {
  margin-left: 4px;
}

.dropdown-arrow {
  font-size: 0.8rem;
  color: var(--text-secondary);
}

/* Hero Section */
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #0f0f23 0%, #1a1b2e 50%, #2d2e42 100%);
  overflow: hidden;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(217, 70, 239, 0.1) 0%, transparent 50%);
}

.hero-particles {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(2px 2px at 20px 30px, rgba(99, 102, 241, 0.6), transparent),
    radial-gradient(2px 2px at 40px 70px, rgba(139, 92, 246, 0.4), transparent),
    radial-gradient(1px 1px at 90px 40px, rgba(217, 70, 239, 0.3), transparent);
  background-repeat: repeat;
  background-size: 200px 200px;
  animation: particles 20s linear infinite;
}

@keyframes particles {
  from { transform: translateY(0px); }
  to { transform: translateY(200px); }
}

.hero-content {
  position: relative;
  z-index: 2;
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

.hero-badge {
  display: inline-block;
  background: rgba(99, 102, 241, 0.2);
  border: 1px solid rgba(99, 102, 241, 0.4);
  color: var(--primary);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 2rem;
  backdrop-filter: blur(10px);
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 800;
  line-height: 1.1;
  margin-bottom: 1.5rem;
}

.gradient-text {
  background: var(--gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-description {
  font-size: 1.25rem;
  color: var(--text-secondary);
  margin-bottom: 3rem;
  line-height: 1.6;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-bottom: 4rem;
  flex-wrap: wrap;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 3rem;
  flex-wrap: wrap;
}

.stat {
  text-align: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

/* Main Content */
.main {
  margin-top: 70px;
  min-height: calc(100vh - 70px);
}

.main.authenticated {
  padding: 2rem 0;
}

/* Section Styles */
.section {
  margin-bottom: 3rem;
}

.section-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.title-icon {
  font-size: 1.5rem;
}

.section-subtitle {
  color: var(--text-secondary);
  font-size: 1.1rem;
}

/* Button Styles */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  font-size: 0.9rem;
}

.btn-primary {
  background: var(--primary);
  color: white;
}

.btn-primary:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.btn-secondary {
  background: var(--surface);
  color: var(--text-primary);
  border: 1px solid var(--border);
}

.btn-secondary:hover {
  background: var(--surface-light);
  border-color: var(--primary);
}

.btn-outline {
  background: transparent;
  color: var(--text-primary);
  border: 1px solid var(--border);
}

.btn-outline:hover {
  background: var(--surface);
  border-color: var(--primary);
}

.btn-text {
  background: transparent;
  color: var(--text-secondary);
  border: none;
}

.btn-text:hover {
  color: var(--text-primary);
}

.btn-white {
  background: white;
  color: var(--background);
}

.btn-white:hover {
  background: #f8fafc;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 0.8rem;
}

.btn-large {
  padding: 16px 32px;
  font-size: 1rem;
}

.btn-full {
  width: 100%;
  justify-content: center;
}

.btn-icon {
  font-size: 1rem;
}

/* Card Styles */
.users-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.user-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.user-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--gradient);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.user-card:hover::before {
  transform: scaleX(1);
}

.user-card:hover {
  transform: translateY(-4px);
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

.user-card.selected {
  border-color: var(--primary);
  background: rgba(99, 102, 241, 0.05);
}

.user-card.selected::before {
  transform: scaleX(1);
}

.user-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.user-badge .badge {
  background: var(--success);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 500;
}

.user-card-body {
  text-align: center;
}

.user-name {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.user-email {
  color: var(--text-secondary);
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.user-stats {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.user-stat {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--text-muted);
  font-size: 0.8rem;
}

/* Reviews Grid */
.reviews-grid {
  display: grid;
  gap: 1.5rem;
}

.review-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.review-card:hover {
  transform: translateY(-2px);
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

.review-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.game-info {
  flex: 1;
}

.game-title {
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.game-rating {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.rating-stars {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 0.9rem;
  opacity: 0.3;
}

.star.filled {
  opacity: 1;
  color: var(--secondary);
}

.rating-score {
  background: var(--primary);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
}

.review-title {
  font-size: 1.1rem;
  font-weight: 500;
  margin-bottom: 1rem;
  color: var(--text-primary);
}

.review-content {
  color: var(--text-secondary);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.rating-breakdown {
  background: var(--surface-light);
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1rem;
}

.rating-category {
  margin-bottom: 0.75rem;
}

.rating-category:last-child {
  margin-bottom: 0;
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.category-name {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.category-rating {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-primary);
}

.rating-bar {
  height: 6px;
  background: var(--border);
  border-radius: 3px;
  overflow: hidden;
}

.rating-progress {
  height: 100%;
  border-radius: 3px;
  transition: width 0.5s ease;
}

.rating-progress.gameplay_rating {
  background: var(--primary);
}

.rating-progress.graphics_rating {
  background: #8b5cf6;
}

.rating-progress.story_rating {
  background: #f59e0b;
}

.rating-progress.music_rating {
  background: #10b981;
}

.rating-progress.atmosphere_rating {
  background: #d946ef;
}

.review-footer {
  border-top: 1px solid var(--border);
  padding-top: 1rem;
}

.review-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: var(--text-muted);
}

/* Search Section */
.search-section {
  margin-bottom: 2rem;
}

.search-box {
  display: flex;
  gap: 1rem;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 2rem;
  align-items: center;
}

.search-icon {
  font-size: 1.5rem;
  color: var(--text-secondary);
}

.search-input {
  flex: 1;
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-size: 1rem;
  outline: none;
}

.search-input::placeholder {
  color: var(--text-muted);
}

.search-btn {
  white-space: nowrap;
}

.games-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.game-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.game-card:hover {
  transform: translateY(-4px);
  border-color: var(--primary);
  box-shadow: var(--shadow);
}

.game-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.game-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.game-card:hover .game-image img {
  transform: scale(1.05);
}

.game-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.game-card:hover .game-overlay {
  opacity: 1;
}

.game-content {
  padding: 1.5rem;
}

.game-title {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.game-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.meta-icon {
  font-size: 0.8rem;
}

.game-genres {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.genre-tag {
  background: var(--surface-light);
  color: var(--text-secondary);
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
}

/* Profile Styles */
.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 3rem;
  flex-wrap: wrap;
  gap: 2rem;
}

.profile-avatar-section {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.profile-email {
  color: var(--text-secondary);
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.profile-join-date {
  color: var(--text-muted);
  font-size: 0.9rem;
}

.profile-stats {
  margin-bottom: 3rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow);
}

.stat-card.primary {
  border-left: 4px solid var(--primary);
}

.stat-card.success {
  border-left: 4px solid var(--success);
}

.stat-card.warning {
  border-left: 4px solid var(--warning);
}

.stat-card.info {
  border-left: 4px solid var(--primary);
}

.stat-icon {
  font-size: 2rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--surface-light);
  border-radius: 12px;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.profile-content {
  display: grid;
  gap: 2rem;
}

.content-section {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 2rem;
}

.reviews-list.compact {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review-item.compact {
  background: var(--surface-light);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1.5rem;
  transition: all 0.3s ease;
}

.review-item.compact:hover {
  border-color: var(--primary);
  transform: translateX(4px);
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.review-rating {
  background: var(--primary);
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
}

.review-excerpt {
  color: var(--text-secondary);
  line-height: 1.5;
  margin-bottom: 0.75rem;
}

.review-date {
  color: var(--text-muted);
  font-size: 0.8rem;
}

/* Loading and Empty States */
.loading-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid var(--border);
  border-top: 3px solid var(--primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.empty-state.small {
  padding: 2rem;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h3 {
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  backdrop-filter: blur(10px);
}

.modal {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 16px;
  width: 90%;
  max-width: 400px;
  box-shadow: var(--shadow);
  animation: modalAppear 0.3s ease;
}

@keyframes modalAppear {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border);
}

.modal-header h2 {
  font-size: 1.5rem;
  font-weight: 600;
}

.modal-close {
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}

.modal-close:hover {
  background: var(--surface-light);
  color: var(--text-primary);
}

.modal-form {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-primary);
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: 12px;
  background: var(--surface-light);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.error-message {
  color: var(--error);
  text-align: center;
  margin: 1rem 0;
  font-size: 0.9rem;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid var(--border);
  text-align: center;
  color: var(--text-secondary);
}

.link-btn {
  background: none;
  border: none;
  color: var(--primary);
  cursor: pointer;
  text-decoration: underline;
}

.link-btn:hover {
  color: var(--primary-dark);
}

/* Responsive Design */
@media (max-width: 768px) {
  .container {
    padding: 0 16px;
  }
  
  .header-content {
    flex-wrap: wrap;
    height: auto;
    padding: 1rem 0;
  }
  
  .nav {
    order: 3;
    width: 100%;
    margin-top: 1rem;
    justify-content: center;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-stats {
    gap: 2rem;
  }
  
  .section-title {
    font-size: 1.5rem;
  }
  
  .users-grid {
    grid-template-columns: 1fr;
  }
  
  .games-grid {
    grid-template-columns: 1fr;
  }
  
  .profile-header {
    flex-direction: column;
    text-align: center;
  }
  
  .profile-avatar-section {
    flex-direction: column;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .search-box {
    flex-direction: column;
  }
  
  .review-card-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .game-rating {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-description {
    font-size: 1rem;
  }
  
  .section-title {
    font-size: 1.25rem;
  }
  
  .user-card,
  .review-card,
  .game-card {
    padding: 1rem;
  }
}
</style>