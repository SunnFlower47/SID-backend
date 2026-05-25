<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon & PWA -->
        <link rel="icon" type="image/png" href="/assets/images/logo-desa-cibatu.png">
        <link rel="apple-touch-icon" href="/assets/images/logo-desa-cibatu.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#16a34a">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @routes(nonce: $csp_nonce ?? null)
        @viteReactRefresh
        @vite(['resources/js/inertia-app.jsx'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        
        <!-- PWA Service Worker -->
        <script nonce="{{ $csp_nonce ?? '' }}">
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </body>
</html>
