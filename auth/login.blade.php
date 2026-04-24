<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin - Desa Cibatu</title>
    <meta name="description" content="Login Admin Panel Sistem Informasi Desa Cibatu, Purwakarta">
    <meta name="keywords" content="login admin, desa cibatu, purwakarta, sistem informasi desa">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-desa-cibatu.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- reCAPTCHA v3 Only -->
    @php
        $isLocalhost = request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' || str_contains(request()->getHost(), 'localhost');
        $recaptchaSiteKey = $isLocalhost ? '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI' : config('services.recaptcha.v3_site_key');
    @endphp
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen">
    <!-- Background with Pattern -->
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-indigo-100 bg-pattern flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-6 sm:space-y-8">
            <!-- Header -->
            <div class="text-center fade-in">
                <div class="flex items-center justify-center space-x-2 sm:space-x-3 mb-4 sm:mb-6">
                    <img src="{{ asset('logo-desa-cibatu.png') }}" alt="Logo Desa Cibatu" class="h-10 w-10 sm:h-12 sm:w-12 rounded-lg" loading="lazy" decoding="async">
                    <div class="text-left">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Admin Panel</h1>
                        <p class="text-xs sm:text-sm text-gray-600">Desa Cibatu, Purwakarta</p>
                    </div>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Masuk ke Dashboard</h2>
                <p class="text-sm sm:text-base text-gray-600">Kelola sistem administrasi desa dengan mudah</p>
            </div>

            <!-- Login Form -->
            <div class="card-hover bg-white py-6 sm:py-8 lg:py-10 px-6 sm:px-8 lg:px-10 shadow-2xl rounded-2xl sm:rounded-3xl border border-gray-100 fade-in">
                <form class="space-y-6 sm:space-y-8" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-800 mb-2 sm:mb-3">Email Admin</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   class="appearance-none block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 @error('email') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                                   placeholder="admin@desacibatu.com" value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <p class="mt-2 sm:mt-3 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-2 sm:mb-3">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="appearance-none block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 @error('password') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                                   placeholder="Masukkan password">
                        </div>
                        @error('password')
                            <p class="mt-2 sm:mt-3 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- reCAPTCHA v3 - Invisible, no display needed -->
                    @if(!$recaptchaSiteKey || !config('services.recaptcha.v3_secret_key'))
                    <!-- reCAPTCHA Keys Not Configured -->
                    <div class="flex justify-center">
                        <div class="px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            reCAPTCHA Keys Not Configured - Add RECAPTCHA_V3_SITE_KEY and RECAPTCHA_V3_SECRET_KEY to .env
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                   class="h-4 w-4 sm:h-5 sm:w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded-lg transition-colors">
                            <label for="remember" class="ml-2 sm:ml-3 block text-sm font-medium text-gray-700">
                                Ingat saya
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <div class="text-sm">
                                <a href="{{ route('password.request') }}" class="font-semibold text-green-600 hover:text-green-700 transition-colors">
                                    Lupa password?
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 sm:pt-4">
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 sm:py-4 px-4 sm:px-6 border border-transparent text-sm sm:text-base font-bold rounded-xl sm:rounded-2xl text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-4 focus:ring-green-200 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-0.5">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-4 sm:pl-6">
                                <i class="fas fa-sign-in-alt text-green-100 group-hover:text-white transition-colors text-sm sm:text-base"></i>
                            </span>
                            <span class="flex items-center text-base sm:text-lg">
                                <i class="fas fa-arrow-right mr-2 sm:mr-3"></i>
                                <span class="hidden sm:inline">Masuk ke Dashboard Admin</span>
                                <span class="sm:hidden">Masuk Admin</span>
                            </span>
                        </button>
                    </div>
                </form>


                <!-- Back to Welcome -->
                <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-100">
                    <div class="text-center">
                        <a href="{{ route('welcome') }}" class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg sm:rounded-xl transition-all duration-200">
                            <i class="fas fa-arrow-left mr-1 sm:mr-2"></i>
                            <span class="hidden sm:inline">Kembali ke Halaman Utama</span>
                            <span class="sm:hidden">Kembali</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for animations and reCAPTCHA v3 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to elements
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((element, index) => {
                setTimeout(() => {
                    element.classList.add('active');
                }, index * 200);
            });

            // Skip reCAPTCHA completely for development
            const isLocalhost = true; // Force skip CAPTCHA for now

            if (!isLocalhost) {
                // reCAPTCHA v3 implementation for production
                const siteKey = '{{ $recaptchaSiteKey }}';
                if (siteKey && typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(function() {
                        // Execute reCAPTCHA v3 on form submit
                        const form = document.querySelector('form[method="POST"]');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();

                                grecaptcha.execute(siteKey, { action: 'login' }).then(function(token) {
                                    // Add token to form
                                    const tokenInput = document.createElement('input');
                                    tokenInput.type = 'hidden';
                                    tokenInput.name = 'recaptcha_token';
                                    tokenInput.value = token;
                                    form.appendChild(tokenInput);

                                    // Submit form
                                    form.submit();
                                }).catch(function(error) {
                                    console.error('reCAPTCHA v3 error:', error);
                                    // Still submit form if reCAPTCHA fails
                                    form.submit();
                                });
                            });
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>

