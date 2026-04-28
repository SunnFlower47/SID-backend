<!-- Top Navbar -->
<nav class="bg-white/95 backdrop-blur-md border-b border-gray-200/50 shadow-lg fixed top-0 left-0 right-0 z-30 header-responsive transition-transform duration-300 ease-in-out" id="navbar">
    <div class="flex items-center justify-between px-3 sm:px-6 py-3 sm:py-4">
        <!-- Left side - Page title with breadcrumb -->
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Mobile Menu Button (hidden on desktop) -->
            <button id="mobile-menu-button" class="lg:hidden p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200">
                <i class="fas fa-bars text-base sm:text-lg"></i>
            </button>

            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-home text-white text-sm sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1 max-w-xs sm:max-w-none">
                    <h1 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
                </div>
            </div>
        </div>

        <!-- Right side - User actions -->
        <div class="flex items-center space-x-1 sm:space-x-3">
            <!-- Search Button -->
            <div class="relative" x-data="menuSearch()" x-init="init()" x-cloak>
                <button id="search-toggle" @click="toggleSearch()" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200" title="Cari menu">
                    <i class="fas fa-search text-base sm:text-lg"></i>
            </button>

                <!-- Mobile Overlay for Search -->
                <div id="search-overlay" x-show="isOpen" @click="isOpen = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/20 z-40 lg:hidden"></div>

                <!-- Search Dropdown -->
                <div id="search-menu" x-show="isOpen" @click.away="isOpen = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                     class="absolute mt-2 bg-white rounded-xl shadow-xl border border-gray-200/50 z-50 max-h-80 overflow-hidden backdrop-blur-sm"
                     style="right: 0.5rem; width: calc(100vw - 3rem); max-width: 18rem; min-width: 16rem;">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Cari Menu</h3>
                            <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-md hover:bg-gray-100">
                                <i class="fas fa-times text-sm"></i>
            </button>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="px-4 py-3">
                        <div class="relative">
                            <input type="text"
                                   x-model="searchQuery"
                                   @input="searchMenus()"
                                   placeholder="Ketik nama menu..."
                                   class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 text-sm">
                            <i class="fas fa-search absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div class="py-2 max-h-40 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 w-full">
                        <template x-for="menu in filteredMenus" :key="menu.id">
                            <a :href="menu.url" @click="isOpen = false"
                               class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors duration-200 w-full group border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mr-3"
                                     :class="menu.iconBg">
                                    <i :class="menu.icon" :class="menu.iconColor + ' text-sm'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-green-700" x-text="menu.name"></p>
                                    <p class="text-xs text-gray-500 truncate mt-1" x-text="menu.description"></p>
                                </div>
                            </a>
                        </template>
                        <div x-show="hasMoreResults" class="text-center text-gray-500 py-3 text-xs border-t border-gray-100 bg-gray-50/50 mx-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Ketik lebih spesifik untuk hasil lainnya
                        </div>
                        <div x-show="filteredMenus.length === 0 && searchQuery.length > 0" class="text-center text-gray-500 py-6 text-sm">
                            <i class="fas fa-search text-gray-300 text-lg mb-2"></i>
                            <div>Menu tidak ditemukan</div>
                            <div class="text-xs mt-1">Coba kata kunci lain</div>
                        </div>
                        <div x-show="searchQuery.length === 0" class="text-center text-gray-500 py-6 text-sm">
                            <i class="fas fa-keyboard text-gray-300 text-lg mb-2"></i>
                            <div>Ketik untuk mencari menu</div>
                            <div class="text-xs mt-1">Mulai mengetik di atas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="relative" x-data="notificationDropdown()" x-init="init()" x-cloak>
                <button id="notif-toggle" @click="toggleNotifications()" class="relative p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200" title="Notifikasi">
                    <i class="fas fa-bell text-base sm:text-lg"></i>
                    <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse text-xs text-white flex items-center justify-center" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                </button>

                <!-- Mobile Overlay for Notifications -->
                <div id="notif-overlay" x-show="isOpen" @click="isOpen = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/20 z-40 lg:hidden"></div>

                <!-- Notification Dropdown -->
                <div id="notif-menu" x-show="isOpen" @click.away="isOpen = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-xl shadow-xl border border-gray-200/50 z-50 max-h-64 overflow-hidden backdrop-blur-sm min-w-72 sm:min-w-80">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-bell text-green-600 text-sm"></i>
                                <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                            </div>
                            <span x-show="unreadCount > 0"
                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                  x-text="unreadCount + ' baru'"></span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="loading" class="p-3 text-center">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-green-600 mx-auto"></div>
                        <p class="text-xs text-gray-500 mt-1">Memuat...</p>
                    </div>

                    <!-- Notifications List -->
                    <div x-show="!loading" class="max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 hover:scrollbar-thumb-gray-400">
                        <template x-for="notification in notifications" :key="notification.id">
                            <div class="px-4 py-4 border-b border-gray-100 hover:bg-gradient-to-r hover:from-green-50 hover:to-blue-50 transition-all duration-200 cursor-pointer group"
                                 @click="markAsRead(notification)">
                                <div class="flex items-start space-x-4">
                                    <!-- Icon with background -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 group-hover:scale-110"
                                             :class="{
                                                 'bg-yellow-100 group-hover:bg-yellow-200': notification.status === 'unread' || notification.status === 'pending',
                                                 'bg-blue-100 group-hover:bg-blue-200': notification.status === 'read' || notification.status === 'diproses',
                                                 'bg-green-100 group-hover:bg-green-200': notification.status === 'replied' || notification.status === 'selesai',
                                                 'bg-red-100 group-hover:bg-red-200': notification.status === 'ditolak',
                                                 'bg-gray-100 group-hover:bg-gray-200': notification.status === 'archived'
                                             }">
                                            <i :class="notification.icon"
                                               :class="{
                                                   'text-yellow-600': notification.status === 'unread' || notification.status === 'pending',
                                                   'text-blue-600': notification.status === 'read' || notification.status === 'diproses',
                                                   'text-green-600': notification.status === 'replied' || notification.status === 'selesai',
                                                   'text-red-600': notification.status === 'ditolak',
                                                   'text-gray-600': notification.status === 'archived'
                                               }"
                                               class="text-lg"></i>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-sm font-semibold text-gray-900 group-hover:text-green-700 transition-colors"
                                               x-text="notification.title"></p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-all duration-200"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-800 group-hover:bg-yellow-200': notification.status === 'unread' || notification.status === 'pending',
                                                      'bg-blue-100 text-blue-800 group-hover:bg-blue-200': notification.status === 'read' || notification.status === 'diproses',
                                                      'bg-green-100 text-green-800 group-hover:bg-green-200': notification.status === 'replied' || notification.status === 'selesai',
                                                      'bg-red-100 text-red-800 group-hover:bg-red-200': notification.status === 'ditolak',
                                                      'bg-gray-100 text-gray-800 group-hover:bg-gray-200': notification.status === 'archived'
                                                  }"
                                                  x-text="getStatusLabel(notification.status)"></span>
                                        </div>
                                        <p class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors leading-relaxed"
                                           x-text="notification.message"></p>
                                        <div class="flex items-center mt-2 space-x-2">
                                            <i class="fas fa-clock text-xs text-gray-400"></i>
                                            <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors"
                                               x-text="notification.time"></p>
                                        </div>
                                    </div>

                                    <!-- Arrow indicator -->
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-500 transition-colors text-sm"></i>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="notifications.length === 0" class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">Tidak ada notifikasi</h4>
                            <p class="text-sm text-gray-500">Semua notifikasi sudah dibaca atau belum ada aktivitas terbaru</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('contact-messages.index') }}" class="flex items-center space-x-2 text-sm text-green-600 hover:text-green-700 font-semibold transition-colors">
                                <i class="fas fa-envelope"></i>
                                <span>Lihat semua pesan</span>
                            </a>
                            <button @click="isOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" x-cloak>
                    <button id="profile-toggle" @click="open = !open" class="flex items-center space-x-2 sm:space-x-3 p-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all duration-200">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center shadow-lg flex-shrink-0">
                            <i class="fas fa-user text-white text-sm sm:text-base"></i>
                        </div>
                        <div class="hidden sm:block text-left min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 flex-shrink-0" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Mobile Overlay for Profile -->
                    <div id="profile-overlay" x-show="open" @click="open = false"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-black/20 z-40 lg:hidden"></div>

                <!-- User Dropdown Menu -->
                <div id="profile-menu" x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-gray-200/50 z-50 backdrop-blur-sm">

                    <!-- User Info Header -->
                    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-600 to-green-700">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-user text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white">{{ auth()->user()->name }}</h3>
                                <p class="text-sm text-green-100">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-green-200 font-medium">{{ auth()->user()->roles->first()->name ?? 'User' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-3">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-user-edit text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Edit Profil</span>
                                <p class="text-xs text-gray-500">Ubah informasi pribadi</p>
                            </div>
                        </a>
                        <a href="{{ route('settings.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-cog text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Pengaturan</span>
                                <p class="text-xs text-gray-500">Konfigurasi sistem</p>
                            </div>
                        </a>
                        <a href="{{ route('audit-log.index') }}" class="flex items-center px-6 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-history text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Aktivitas Saya</span>
                                <p class="text-xs text-gray-500">Riwayat aktivitas</p>
                            </div>
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="border-t border-gray-100 py-3">
                        <button type="button" id="logout-button" class="flex items-center w-full px-6 py-3 text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-sign-out-alt text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Logout</span>
                                <p class="text-xs text-gray-500">Keluar dari sistem</p>
                            </div>
                        </button>
                    </div>

                    <script nonce="{{ $csp_nonce }}">
                    document.addEventListener('DOMContentLoaded', function() {
                        const logoutBtn = document.getElementById('logout-button');
                        if (logoutBtn) {
                            logoutBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                
                                // Create form dynamically
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '{{ route("logout") }}';

                                // Add CSRF token
                                const csrfToken = document.createElement('input');
                                csrfToken.type = 'hidden';
                                csrfToken.name = '_token';
                                csrfToken.value = '{{ csrf_token() }}';
                                form.appendChild(csrfToken);

                                // Add to body and submit
                                document.body.appendChild(form);
                                form.submit();
                            });
                        }
                    });
                    </script>
                </div>
            </div>

        </div>
    </div>
</nav>

