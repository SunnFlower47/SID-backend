<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status_code ?? 'Error' }} - Kesalahan | Desa Cibatu</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Icon -->
            <div class="mx-auto h-24 w-24 text-green-500">
                <i class="fas fa-exclamation-circle text-6xl"></i>
            </div>

            <!-- Error Code -->
            <div>
                <h1 class="text-6xl font-bold text-green-600 mb-2">{{ $status_code ?? 'Error' }}</h1>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Terjadi Kesalahan</h2>
                <p class="text-gray-600 mb-6">{{ $message ?? 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.' }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <button onclick="window.location.reload()"
                        class="w-full flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Coba Lagi
                </button>

                <a href="{{ url()->previous() }}"
                   class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Halaman Sebelumnya
                </a>

                <a href="{{ route('welcome') }}"
                   class="w-full flex justify-center items-center px-4 py-3 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Help Text -->
            <div class="mt-8 p-4 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm text-green-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Jika masalah berlanjut, silakan hubungi administrator.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

