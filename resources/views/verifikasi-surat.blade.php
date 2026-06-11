<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian Surat - Desa Cibatu</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 font-sans">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        
        <div class="bg-indigo-600 p-6 text-center text-white">
            <h1 class="text-xl font-bold uppercase tracking-widest">Verifikasi Surat</h1>
            <p class="text-indigo-200 text-sm mt-1">Sistem Informasi Desa Cibatu</p>
        </div>

        <div class="p-8 text-center space-y-6">
            @if($success && $data)
                <!-- Success State -->
                <div class="flex justify-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-2xl font-black text-gray-800">Surat Asli</h2>
                <p class="text-gray-500 text-sm">Dokumen ini diterbitkan secara resmi oleh Pemerintah Desa {{ $data['desa'] ?? 'Cibatu' }} dan tercatat dalam sistem.</p>

                <div class="bg-gray-50 rounded-2xl p-4 text-left border border-gray-100 space-y-3">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Surat</span>
                        <span class="block text-sm font-bold text-gray-900">{{ $data['nomor_surat'] }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis Surat</span>
                        <span class="block text-sm font-bold text-gray-900">{{ $data['jenis_surat'] }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Surat</span>
                        <span class="block text-sm font-bold text-gray-900">{{ $data['tanggal_surat'] ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Atas Nama</span>
                        <span class="block text-sm font-bold text-gray-900">{{ $data['nama_pemohon'] }}</span>
                    </div>
                </div>

                @if(isset($data['is_tte']) && $data['is_tte'])
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        @if(isset($data['is_tte_signed']) && $data['is_tte_signed'])
                            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-4 text-left">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-green-800 uppercase tracking-widest">Ditandatangani Elektronik</h4>
                                    <p class="text-[10px] font-bold text-green-600 mt-0.5">Dokumen ini telah ditandatangani secara elektronik tersertifikasi oleh BSrE BSSN.</p>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-center gap-4 text-left">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-yellow-800 uppercase tracking-widest">Menunggu TTE</h4>
                                    <p class="text-[10px] font-bold text-yellow-600 mt-0.5">Dokumen ini sedang dalam proses antrean untuk penandatanganan elektronik.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <!-- Failed / Not Found State -->
                <div class="flex justify-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                
                <h2 class="text-2xl font-black text-gray-800">Surat Tidak Valid</h2>
                <p class="text-gray-500 text-sm">{{ $message ?? 'Dokumen tidak ditemukan atau tidak tercatat dalam sistem kami. Harap berhati-hati terhadap pemalsuan dokumen.' }}</p>

            @endif
        </div>
        
        <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} Pemerintah Desa Cibatu</p>
        </div>

    </div>

</body>
</html>
