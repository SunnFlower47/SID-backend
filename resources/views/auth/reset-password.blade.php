<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Password - Desa Cibatu</title>
    <meta name="description" content="Reset Password Admin Panel Sistem Informasi Desa Cibatu, Purwakarta">
    <meta name="keywords" content="reset password, desa cibatu, purwakarta">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-desa-cibatu.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #ef4444; }
        .strength-medium { background: #f59e0b; }
        .strength-strong { background: #10b981; }
    </style>
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="bg-white p-4 rounded-2xl shadow-lg">
                    <img src="{{ asset('logo-desa-cibatu.png') }}" alt="Logo Desa Cibatu" class="h-16 w-16 mx-auto">
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h2>
                <p class="text-gray-600 text-sm sm:text-base">Masukkan password baru untuk akun Anda</p>
                <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <p class="text-yellow-800 text-sm">
                        <i class="fas fa-clock mr-2"></i>
                        Link ini berlaku selama 5 menit
                    </p>
                </div>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Success Message -->
            @if (session('status'))
                <div class="success-message rounded-xl p-4 mb-6 fade-in">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-green-800">Password Berhasil Direset!</h3>
                            <p class="text-green-700 text-sm mt-1">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

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

            <!-- Reset Form -->
            <div class="card-hover bg-white py-6 sm:py-8 lg:py-10 px-6 sm:px-8 lg:px-10 shadow-2xl rounded-2xl sm:rounded-3xl border border-gray-100 fade-in">
                <form class="space-y-6 sm:space-y-8" method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-800 mb-2 sm:mb-3">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required readonly
                                   class="appearance-none block w-full pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 bg-gray-50 text-gray-600 text-sm sm:text-base @error('email') border-red-300 @enderror"
                                   value="{{ old('email', $request->email) }}">
                        </div>
                        @error('email')
                            <p class="mt-2 sm:mt-3 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-2 sm:mb-3">
                            <i class="fas fa-lock mr-2"></i>Password Baru
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                   class="appearance-none block w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 @error('password') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                                   placeholder="Masukkan password baru">
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center">
                                <button type="button" onclick="togglePassword('password')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="flex space-x-1">
                                <div id="strength-1" class="password-strength flex-1 bg-gray-200"></div>
                                <div id="strength-2" class="password-strength flex-1 bg-gray-200"></div>
                                <div id="strength-3" class="password-strength flex-1 bg-gray-200"></div>
                                <div id="strength-4" class="password-strength flex-1 bg-gray-200"></div>
                            </div>
                            <p id="strength-text" class="text-xs text-gray-500 mt-1">Masukkan password untuk melihat kekuatan</p>
                        </div>

                        @error('password')
                            <p class="mt-2 sm:mt-3 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2 sm:mb-3">
                            <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-green-500 transition-colors text-sm sm:text-base"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                   class="appearance-none block w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-3 sm:py-4 border-2 border-gray-200 rounded-xl sm:rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 text-sm sm:text-base transition-all duration-300 @error('password_confirmation') border-red-300 focus:ring-red-100 focus:border-red-500 @enderror"
                                   placeholder="Ulangi password baru">
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i id="password_confirmation-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 sm:mt-3 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- reCAPTCHA - Show if keys are configured -->
                    @if(config('services.recaptcha.v2_site_key') && config('services.recaptcha.v2_secret_key') && !app()->environment('local', 'testing'))
                    <div class="flex justify-center">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.v2_site_key') }}"></div>
                    </div>
                    @error('g-recaptcha-response')
                        <p class="mt-2 text-sm text-red-600 flex items-center justify-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    @elseif(!app()->environment('local', 'testing'))
                    <!-- reCAPTCHA Keys Not Configured -->
                    <div class="flex justify-center">
                        <div class="px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            reCAPTCHA Keys Not Configured - Add RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY to .env
                        </div>
                    </div>
                    @endif

                    <div class="pt-2 sm:pt-4">
                        <button type="submit" class="btn-primary group relative w-full flex justify-center py-3 sm:py-4 px-4 border border-transparent text-sm sm:text-base font-semibold rounded-xl sm:rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-save text-green-500 group-hover:text-green-400 transition-colors"></i>
                            </span>
                            <span class="ml-2">Reset Password</span>
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="btn-secondary group relative inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>

            <!-- Password Requirements -->
            <div class="mt-6">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h3 class="text-gray-800 font-semibold mb-2">
                        <i class="fas fa-shield-alt mr-2 text-blue-500"></i>Persyaratan Password:
                    </h3>
                    <ul class="text-gray-600 text-sm space-y-1">
                        <li>• Minimal 8 karakter</li>
                        <li>• Mengandung huruf besar dan kecil</li>
                        <li>• Mengandung angka</li>
                        <li>• Mengandung karakter khusus</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updateStrengthIndicator(strength);
        });

        function checkPasswordStrength(password) {
            let score = 0;

            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            return Math.min(score, 4);
        }

        function updateStrengthIndicator(strength) {
            const indicators = ['strength-1', 'strength-2', 'strength-3', 'strength-4'];
            const texts = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'];
            const colors = ['strength-weak', 'strength-weak', 'strength-medium', 'strength-strong'];

            indicators.forEach((id, index) => {
                const element = document.getElementById(id);
                element.className = 'password-strength flex-1';

                if (index < strength) {
                    element.classList.add(colors[strength - 1]);
                } else {
                    element.classList.add('bg-gray-200');
                }
            });

            const textElement = document.getElementById('strength-text');
            if (strength > 0) {
                textElement.textContent = `Kekuatan: ${texts[strength - 1]}`;
                textElement.className = strength >= 3 ? 'text-xs text-green-600 mt-1' : 'text-xs text-red-600 mt-1';
            } else {
                textElement.textContent = 'Masukkan password untuk melihat kekuatan';
                textElement.className = 'text-xs text-gray-500 mt-1';
            }
        }
    </script>
</body>
</html>

