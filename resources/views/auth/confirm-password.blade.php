<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Konfirmasi Password - Desa Cibatu</title>
    <meta name="description" content="Konfirmasi Password Admin Panel Sistem Informasi Desa Cibatu, Purwakarta">
    <meta name="keywords" content="konfirmasi password, desa cibatu, purwakarta">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-desa-cibatu.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: white;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-1px);
        }

        .success-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            color: #065f46;
        }

        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 1px solid #ef4444;
            color: #991b1b;
        }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="bg-white p-4 rounded-2xl shadow-lg">
                    <img src="{{ asset('logo-desa-cibatu.png') }}" alt="Logo Desa Cibatu" class="h-16 w-16 mx-auto">
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Konfirmasi Password</h2>
                <p class="text-gray-600 text-sm sm:text-base">Masukkan password untuk melanjutkan</p>
            </div>

            <!-- Error Message -->
            @if ($errors->any())
                <div class="error-message rounded-xl p-4 mb-6 fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-red-800">Terjadi Kesalahan!</h3>
                            <ul class="text-red-700 text-sm mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Confirm Form -->
            <div class="card-hover bg-white py-8 px-8 shadow-2xl rounded-2xl border border-gray-100 fade-in">
                <form class="space-y-6" method="POST" action="{{ route('password.confirm') }}">
                    @csrf

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

                    <div class="pt-4">
                        <button type="submit" class="btn-primary group relative w-full flex justify-center py-4 px-4 border border-transparent text-base font-semibold rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-check text-green-500 group-hover:text-green-400 transition-colors"></i>
                            </span>
                            <span class="ml-2">Konfirmasi</span>
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <a href="{{ route('logout') }}" class="btn-secondary group relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </a>
                    </div>
                </form>
            </div>

            <!-- Info -->
            <div class="text-center">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-600 text-sm">
                        <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                        Konfirmasi password diperlukan untuk keamanan
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

