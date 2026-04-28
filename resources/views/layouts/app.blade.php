<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Sistem Desa Cibatu">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Desa Cibatu">
    <meta name="description" content="Sistem Informasi Desa Cibatu - Purwakarta">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#1e40af">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#1e40af">

    <!-- PWA Install Prompt -->
    <meta name="apple-itunes-app" content="app-id=desa-cibatu-pwa">
    <meta name="google-play-app" content="app-id=desa-cibatu-pwa">

    <!-- PWA Display Mode -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">

    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="/images/icons/icon-152x152.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-16x16.png">
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <link rel="mask-icon" href="/images/icons/safari-pinned-tab.svg" color="#1e40af">
    <link rel="shortcut icon" href="/favicon.ico">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cache Busting for Development -->
    @if(config('app.debug'))
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Session Messages Component -->
    @include('components.session-messages')

    <style>
        /* Custom scrollbar untuk sidebar */
        .scrollbar-thin {
            scrollbar-width: thin;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Main layout container */
        .main-layout-container {
            margin-left: 0;
            min-height: 100vh;
        }

        @media (min-width: 1024px) {
            .main-layout-container {
                margin-left: 16rem; /* 256px = w-64 */
            }
            .header-responsive {
                left: 16rem; /* 256px = w-64 */
            }
        }

        /* Mobile responsive */
        @media (max-width: 1023px) {
            .main-layout-container {
                margin-left: 0;
            }
            .header-responsive {
                left: 0 !important;
            }
        }

        /* Pastikan content tidak overlap dengan header */
        .main-content {
            padding-top: 100px; /* Padding untuk header yang fixed */
            padding-left: 2rem; /* 32px - lebih proporsional */
            padding-right: 2rem; /* 32px - konsisten dengan kiri */
            padding-bottom: 2rem; /* 32px - konsisten */
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    <!-- Mobile Menu Button -->
    @include('layouts.components.mobile-menu')

    <!-- Sidebar -->
    @include('layouts.components.sidebar')

    <!-- Main Layout Container -->
    <div class="flex flex-col min-h-screen main-layout-container">
        <!-- Top Navbar -->
        @include('layouts.components.header')

        <!-- Main Content Area -->
        <main class="flex-1 main-content">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @include('layouts.components.scripts')

    <!-- Auto-format JavaScript with cache busting -->
    <script src="{{ asset('js/auto-format.js') }}?v={{ config('app.debug') ? time() : config('app.version', '1.0.0') }}"></script>
    <script src="{{ asset('js/error-handler.js') }}?v={{ config('app.debug') ? time() : config('app.version', '1.0.0') }}"></script>

    <!-- Page-specific scripts -->
    @stack('scripts')

    <!-- PWA Service Worker Registration -->
    <script nonce="{{ $csp_nonce }}">
        if ('serviceWorker' in navigator) {
            // Development mode detection
            const isDevelopment = {{ config('app.debug') ? 'true' : 'false' }};
            const cacheVersion = '{{ config('app.debug') ? time() : "1.1.0" }}';

            console.log('Environment:', isDevelopment ? 'Development' : 'Production');
            console.log('Cache Version:', cacheVersion);

            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js?v=' + cacheVersion)
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);

                        // Check for updates (no auto-reload)
                        registration.addEventListener('updatefound', function() {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    console.log('Service Worker update found - manual reload required');
                                    // No auto-reload to prevent loops
                                }
                            });
                        });
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });

            // Handle PWA install prompt - Removed duplicate button creation
            // The install button is handled below in the dedicated PWA Install Prompt section

            // Handle PWA installed - Moved to single handler below
        }

        // PWA Install Prompt - Single handler to prevent duplicates
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;

            // Show install button
            const installButton = document.getElementById('install-pwa-button');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the install prompt');
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Aplikasi Desa Cibatu berhasil diinstall!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            console.log('User dismissed the install prompt');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });

        // PWA Install Success - Single handler
        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA was installed');
            const installButton = document.getElementById('install-pwa-button');
            if (installButton) {
                installButton.style.display = 'none';
            }
            Swal.fire({
                title: 'Berhasil!',
                text: 'Aplikasi Desa Cibatu berhasil diinstall!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    </script>

    <!-- PWA Install Button -->
    <div id="install-pwa-button" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg flex items-center space-x-2 transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Install App</span>
        </button>
    </div>
</body>
</html>

