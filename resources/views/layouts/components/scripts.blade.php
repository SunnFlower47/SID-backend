<script nonce="{{ $csp_nonce }}">
    // Check Font Awesome loading
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Font Awesome is loaded
        setTimeout(function() {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-home';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);

            const computedStyle = window.getComputedStyle(testIcon);
            const fontFamily = computedStyle.getPropertyValue('font-family');

            if (!fontFamily.includes('Font Awesome')) {
                // Font Awesome not loaded, add fallback class
                document.querySelectorAll('.fas, .far, .fab').forEach(function(icon) {
                    icon.classList.add('icon-fallback');
                });
            }

            document.body.removeChild(testIcon);
        }, 1000);
    });

    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        console.log('Mobile menu elements:', { mobileMenuButton, sidebar, mobileOverlay });

        if (mobileMenuButton && sidebar) {
            mobileMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Mobile menu button clicked');

                // Toggle sidebar
                sidebar.classList.toggle('-translate-x-full');

                // Toggle overlay
                if (mobileOverlay) {
                    mobileOverlay.classList.toggle('hidden');
                }

                // Sidebar will appear above navbar (no need to hide navbar)

                // Prevent body scroll only when sidebar is open
                if (!sidebar.classList.contains('-translate-x-full')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Close sidebar when clicking overlay
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');

                    // Navbar stays visible (sidebar appears above it)

                    document.body.style.overflow = '';
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024) {
                    if (!sidebar.contains(event.target) &&
                        !mobileMenuButton.contains(event.target) &&
                        !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        if (mobileOverlay) {
                            mobileOverlay.classList.add('hidden');
                        }
                        document.body.style.overflow = '';
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    if (mobileOverlay) {
                        mobileOverlay.classList.add('hidden');
                    }

                    // Navbar stays visible on desktop

                    document.body.style.overflow = '';
                }
            });
        }

        // Mobile sidebar close button
        if (mobileSidebarClose && sidebar) {
            mobileSidebarClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Mobile sidebar close button clicked');

                // Close sidebar
                sidebar.classList.add('-translate-x-full');

                // Hide overlay
                if (mobileOverlay) {
                    mobileOverlay.classList.add('hidden');
                }

                // Restore body scroll
                document.body.style.overflow = '';
            });
        } else {
            console.error('Mobile menu elements not found:', { mobileMenuButton, sidebar, mobileOverlay });
        }
    });

    // Menu Search Component
    function menuSearch() {
        return {
            isOpen: false,
            searchQuery: '',
            filteredMenus: [],
            hasMoreResults: false,
            allMenus: [
                // Data Kependudukan
                { id: 1, name: 'Data Penduduk', description: 'Kelola data penduduk desa', url: '{{ route("penduduk.index") }}', icon: 'fas fa-users', iconBg: 'bg-blue-100', iconColor: 'text-blue-600' },
                { id: 2, name: 'Kartu Keluarga', description: 'Kelola data kartu keluarga', url: '{{ route("kk.index") }}', icon: 'fas fa-id-card', iconBg: 'bg-indigo-100', iconColor: 'text-indigo-600' },

                // Layanan Administrasi
                { id: 3, name: 'Surat Pengajuan', description: 'Kelola surat pengajuan warga', url: '{{ route("admin.surat-pengajuan.index") }}', icon: 'fas fa-file-alt', iconBg: 'bg-green-100', iconColor: 'text-green-600' },
                { id: 4, name: 'Pesan Kontak', description: 'Kelola pesan dari warga', url: '{{ route("contact-messages.index") }}', icon: 'fas fa-envelope', iconBg: 'bg-purple-100', iconColor: 'text-purple-600' },
                { id: 5, name: 'Pengaduan', description: 'Kelola pengaduan warga', url: '{{ route("pengaduan.index") }}', icon: 'fas fa-comments', iconBg: 'bg-yellow-100', iconColor: 'text-yellow-600' },

                // Data Desa
                { id: 6, name: 'Berita', description: 'Kelola berita desa', url: '{{ route("berita.index") }}', icon: 'fas fa-newspaper', iconBg: 'bg-red-100', iconColor: 'text-red-600' },
                { id: 7, name: 'UMKM', description: 'Kelola data UMKM', url: '{{ route("umkm.index") }}', icon: 'fas fa-store', iconBg: 'bg-orange-100', iconColor: 'text-orange-600' },
                { id: 8, name: 'Struktur Desa', description: 'Struktur organisasi desa', url: '{{ route("struktur-desa.index") }}', icon: 'fas fa-sitemap', iconBg: 'bg-cyan-100', iconColor: 'text-cyan-600' },

                // Informasi Desa
                { id: 9, name: 'Kontak Desa', description: 'Informasi kontak desa', url: '{{ route("kontak-desa.index") }}', icon: 'fas fa-phone', iconBg: 'bg-pink-100', iconColor: 'text-pink-600' },
                { id: 10, name: 'Transparansi Desa', description: 'Transparansi keuangan desa', url: '{{ route("transparansi-desa.index") }}', icon: 'fas fa-chart-pie', iconBg: 'bg-violet-100', iconColor: 'text-violet-600' },

                // Laporan & Analisis
                { id: 11, name: 'Laporan Penduduk', description: 'Laporan data penduduk', url: '{{ route("laporan.penduduk") }}', icon: 'fas fa-chart-bar', iconBg: 'bg-sky-100', iconColor: 'text-sky-600' },
                { id: 12, name: 'Laporan Surat', description: 'Laporan surat pengajuan', url: '{{ route("laporan.surat") }}', icon: 'fas fa-file-chart', iconBg: 'bg-rose-100', iconColor: 'text-rose-600' },
                { id: 13, name: 'Dashboard', description: 'Halaman utama dashboard', url: '{{ route("dashboard") }}', icon: 'fas fa-tachometer-alt', iconBg: 'bg-gray-100', iconColor: 'text-gray-600' },
            ],

            init() {
                // Pastikan dropdown tertutup saat init
                this.isOpen = false;
                this.searchQuery = '';
                this.filteredMenus = this.allMenus.slice(0, 5);
                this.hasMoreResults = this.allMenus.length > 5;

                // Force close any open dropdown
                this.$nextTick(() => {
                    this.isOpen = false;
                });
            },

            toggleSearch() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.searchQuery = '';
                    this.filteredMenus = this.allMenus.slice(0, 5);
                    this.hasMoreResults = this.allMenus.length > 5;
                    // Focus on input after dropdown opens
                    this.$nextTick(() => {
                        const input = this.$el.querySelector('input');
                        if (input) input.focus();
                    });
                }
            },

            searchMenus() {
                if (this.searchQuery.trim() === '') {
                    // Show only first 5 menus when no search query
                    this.filteredMenus = this.allMenus.slice(0, 5);
                    this.hasMoreResults = false;
                } else {
                    const query = this.searchQuery.toLowerCase();
                    const filtered = this.allMenus.filter(menu =>
                        menu.name.toLowerCase().includes(query) ||
                        menu.description.toLowerCase().includes(query)
                    );
                    // Show all filtered results, but limit display to 6 for better UX
                    this.filteredMenus = filtered.slice(0, 6);
                    this.hasMoreResults = filtered.length > 6;
                }
            }
        }
    }

    // Notification Dropdown Component
    function notificationDropdown() {
        return {
            isOpen: false,
            loading: false,
            notifications: [],
            unreadCount: 0,

            init() {
                // Pastikan dropdown tertutup saat init
                this.isOpen = false;
                this.loading = false;
                this.notifications = [];
                this.unreadCount = 0;

                // Force close any open dropdown
                this.$nextTick(() => {
                    this.isOpen = false;
                    this.loadNotifications();
                });

                // Auto refresh every 30 seconds
                setInterval(() => {
                    this.loadNotifications();
                }, 30000);
            },

            toggleNotifications() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.loadNotifications();
                }
            },

            async loadNotifications() {
                this.loading = true;
                try {
                    // Load contact messages notifications
                    const response = await fetch('/contact-messages/notifications', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            this.notifications = data.data.notifications;
                            this.unreadCount = data.data.unread_count;
                        }
                    }
                } catch (error) {
                    console.error('Error loading notifications:', error);
                    // Fallback: show empty state
                    this.notifications = [];
                    this.unreadCount = 0;
                } finally {
                    this.loading = false;
                }
            },

            async markAsRead(notification, event) {
                let notificationElement = null;
                let originalContent = '';

                try {
                    // Prevent default behavior and stop propagation
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Add visual feedback
                    notificationElement = event ? event.currentTarget : null;
                    if (notificationElement) {
                        notificationElement.style.transform = 'scale(0.98)';
                        notificationElement.style.opacity = '0.7';
                    }

                    // Show loading state
                    originalContent = notificationElement ? notificationElement.innerHTML : '';
                    if (notificationElement) {
                        notificationElement.innerHTML = `
                            <div class="flex items-center justify-center py-4">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600"></div>
                                <span class="ml-2 text-sm text-gray-600">Membuka...</span>
                            </div>
                        `;
                    }

                    // Determine the correct URL based on notification type
                    let markReadUrl = '';
                    let redirectUrl = '';

                    console.log('Notification data:', notification);
                    console.log('Notification type:', notification.type);
                    console.log('Notification id:', notification.id);

                    switch (notification.type) {
                        case 'contact_message':
                            markReadUrl = `/contact-messages/${notification.id}/mark-read`;
                            redirectUrl = `/contact-messages/${notification.id}`;
                            break;
                        case 'surat_pengajuan':
                            markReadUrl = `/admin/surat-pengajuan/${notification.id}/mark-read`;
                            redirectUrl = `/admin/surat-pengajuan/${notification.id}`;
                            console.log('Surat pengajuan URLs:', { markReadUrl, redirectUrl });
                            break;
                        case 'pengaduan':
                            // Belum ada endpoint mark-read khusus pengaduan
                            // jadi langsung redirect ke detail halaman
                            markReadUrl = '';
                            redirectUrl = `/pengaduan/${notification.id}`;
                            break;
                        default:
                            redirectUrl = notification.url || '#';
                    }

                    // Only make API call if we have a mark-read URL
                    let response = null;
                    if (markReadUrl) {
                        console.log('Making POST request to:', markReadUrl);
                        response = await fetch(markReadUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        console.log('Response status:', response.status);
                    }

                    if (!response || response.ok) {
                        // Update notification status locally
                        const index = this.notifications.findIndex(n => n.id === notification.id);
                        if (index !== -1) {
                            this.notifications[index].status = notification.type === 'contact_message' ? 'read' : 'diproses';
                        }

                        // Close dropdown
                        this.isOpen = false;

                        // Redirect to notification detail
                        if (redirectUrl) {
                            console.log('Redirecting to:', redirectUrl);
                            // Add smooth transition
                            document.body.style.transition = 'opacity 0.3s ease';
                            document.body.style.opacity = '0.8';

                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 300);
                        }
                    } else {
                        // Restore original content on error
                        if (notificationElement) {
                            notificationElement.innerHTML = originalContent;
                            notificationElement.style.transform = '';
                            notificationElement.style.opacity = '';
                        }

                        // Show error message
                        alert('Gagal membuka notifikasi. Silakan coba lagi.');
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);

                    // Restore original content on error
                    if (notificationElement) {
                        notificationElement.style.transform = '';
                        notificationElement.style.opacity = '';
                    }

                    // Show error message
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            },

            getStatusLabel(status) {
                const statusMap = {
                    'unread': 'Baru',
                    'read': 'Dibaca',
                    'replied': 'Dibalas',
                    'pending': 'Menunggu',
                    'diproses': 'Diproses',
                    'selesai': 'Selesai',
                    'ditolak': 'Ditolak',
                    'archived': 'Diarsipkan'
                };
                return statusMap[status] || status;
            }
        }
    }

    // Fallback dropdown handler when Alpine directives are not initialized
    document.addEventListener('DOMContentLoaded', function() {
        const bindFallback = () => {
            const hasAlpineInstance = Array.from(document.querySelectorAll('[x-data]')).some(el => !!el.__x);
            if (hasAlpineInstance) return;

            const bindDropdown = (toggleId, menuId, overlayId) => {
                const toggle = document.getElementById(toggleId);
                const menu = document.getElementById(menuId);
                const overlay = document.getElementById(overlayId);
                if (!toggle || !menu) return;

                const close = () => {
                    menu.style.display = 'none';
                    if (overlay) overlay.style.display = 'none';
                };

                const open = () => {
                    menu.style.display = 'block';
                    if (overlay && window.innerWidth < 1024) overlay.style.display = 'block';
                };

                close();

                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (menu.style.display === 'none' || !menu.style.display) {
                        open();
                    } else {
                        close();
                    }
                });

                if (overlay) {
                    overlay.addEventListener('click', close);
                }

                document.addEventListener('click', function(e) {
                    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                        close();
                    }
                });
            };

            bindDropdown('search-toggle', 'search-menu', 'search-overlay');
            bindDropdown('notif-toggle', 'notif-menu', 'notif-overlay');
            bindDropdown('profile-toggle', 'profile-menu', 'profile-overlay');
        };

        // Delay slightly to allow Alpine init first
        setTimeout(bindFallback, 300);
    });

    // Mark Alpine.js as loaded to prevent splash
    document.addEventListener('alpine:initialized', () => {
        document.querySelectorAll('[x-data]').forEach(el => {
            el.classList.add('alpine-loaded');
            el.style.display = ''; // Remove inline display: none
        });
    });
</script>


