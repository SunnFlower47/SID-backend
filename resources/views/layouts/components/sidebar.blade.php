<!-- Sidebar -->
<div id="sidebar" class="w-64 bg-white/95 backdrop-blur-md border-r border-gray-200/50 fixed left-0 top-0 h-full z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-gray-200/50 flex-shrink-0 bg-gradient-to-br from-white to-gray-50 relative rounded-tr-2xl">
        <div class="flex items-center justify-between">
            <!-- Logo + Identitas -->
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-green-600 rounded-2xl flex items-center justify-center shadow-md">
                    <img src="{{ asset('logo desa cibatu.png') }}" alt="Logo Desa Cibatu" class="w-10 h-10 object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">SID Cibatu</h1>
                    <p class="text-green-600 text-sm font-medium">Sistem Informasi Desa</p>
                    <p class="text-xs text-green-700">Kec. Cibatu, Kab. Purwakarta</p>
                </div>
            </div>

            <!-- Tombol Close Mobile -->
            <button
                id="mobile-sidebar-close"
                class="absolute top-4 right-4 z-50 lg:hidden flex items-center justify-center
                       w-9 h-9 bg-white text-green-700 shadow-sm rounded-xl
                       hover:bg-green-600 hover:text-white transition-all duration-200 ease-in-out focus:ring-2 focus:ring-green-400"
                aria-label="Tutup Sidebar"
            >
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <!-- Dashboard -->
        <div>
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200' : 'text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:shadow-md hover:text-green-700' }}">
                <i class="fas fa-home mr-3 text-lg"></i>
                <span class="font-semibold text-base">Dashboard</span>
            </a>
        </div>

        <!-- Data Kependudukan -->
        <div x-data="{ open: {{ request()->routeIs('penduduk.*') || request()->routeIs('mutasi.*') || request()->routeIs('kartu-keluarga.*') || request()->routeIs('kk.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:shadow-md hover:text-green-700">
                <div class="flex items-center">
                    <i class="fas fa-users mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Data Kependudukan</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('penduduk.view')
                <a href="{{ route('penduduk.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('penduduk.*') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:shadow-md hover:text-green-700' }}">
                    <i class="fas fa-users mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Data Penduduk</span>
                </a>
                @endcan

                @can('mutasi.view')
                <a href="{{ route('mutasi.data.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('mutasi.*') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:shadow-md hover:text-green-700' }}">
                    <i class="fas fa-exchange-alt mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Data Mutasi</span>
                </a>
                @endcan

                @can('kartu-keluarga.view')
                <a href="{{ route('kartu-keluarga.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('kartu-keluarga.*') && !request()->routeIs('kk.*') ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg shadow-green-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 hover:shadow-md hover:text-green-700' }}">
                    <i class="fas fa-id-card mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Kartu Keluarga</span>
                </a>
                @endcan

                @can('kartu-keluarga.view')
                @php $kkBermasalahCount = \App\Models\KartuKeluarga::bermasalah()->count(); @endphp
                <a href="{{ route('kk.bermasalah.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('kk.bermasalah.index') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700' }}">
                    <i class="fas fa-exclamation-triangle mr-3 text-sm {{ $kkBermasalahCount > 0 ? 'animate-pulse' : '' }}"></i>
                    <span class="text-sm font-medium">KK Bermasalah</span>
                    @if($kkBermasalahCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-bold animate-pulse">{{ $kkBermasalahCount }}</span>
                    @endif
                </a>
                @endcan
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200/50 my-4"></div>

        <!-- Layanan Administrasi -->
        <div x-data="{ open: {{ request()->routeIs('admin.surat-pengajuan.*') || request()->routeIs('bantuan-sosial.*') || request()->routeIs('pengaduan.*') || request()->routeIs('contact-messages.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:shadow-md hover:text-purple-700">
                <div class="flex items-center">
                    <i class="fas fa-file-signature mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Layanan Administrasi</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">

                @can('surat.view')
                <a href="{{ route('admin.surat-pengajuan.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.surat-pengajuan.*') ? 'bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg shadow-purple-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:shadow-md hover:text-purple-700' }}">
                    <i class="fas fa-envelope-open-text mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Layanan Surat</span>
                </a>
                @endcan


                @can('bantuan_sosial.view')
                <a href="{{ route('bantuan-sosial.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('bantuan-sosial.*') ? 'bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg shadow-purple-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:shadow-md hover:text-purple-700' }}">
                    <i class="fas fa-hand-holding-heart mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Bantuan Sosial</span>
                </a>
                @endcan

                @can('pengaduan.view')
                <a href="{{ route('pengaduan.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('pengaduan.*') ? 'bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg shadow-purple-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:shadow-md hover:text-purple-700' }}">
                    <i class="fas fa-comments mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Pengaduan Warga</span>
                </a>
                @endcan

                @can('contact-messages.index')
                <a href="{{ route('contact-messages.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('contact-messages.*') ? 'bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg shadow-purple-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100 hover:shadow-md hover:text-purple-700' }}">
                    <i class="fas fa-envelope mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Pesan Kontak</span>
                    @if($unreadContactCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 font-bold">{{ $unreadContactCount }}</span>
                    @endif
                </a>
                @endcan
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200/50 my-4"></div>

        <!-- Data Desa -->
        <div x-data="{ open: {{ request()->routeIs('struktur-desa.*') || request()->routeIs('kontak-desa.*') || request()->routeIs('fasilitas-desa.*') || request()->routeIs('umkm.*') || request()->routeIs('transparansi-desa.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700">
                <div class="flex items-center">
                    <i class="fas fa-building mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Data Desa</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <!-- Divider -->
            <div class="border-t border-gray-200/50 my-4"></div>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('struktur-desa.view')
                <a href="{{ route('struktur-desa.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('struktur-desa.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700' }}">
                    <i class="fas fa-sitemap mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Struktur Desa</span>
                </a>
                @endcan

                @can('kontak-desa.view')
                <a href="{{ route('kontak-desa.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('kontak-desa.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700' }}">
                    <i class="fas fa-address-book mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Kontak Desa</span>
                </a>
                @endcan

                @can('fasilitas-desa.view')
                <a href="{{ route('fasilitas-desa.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('fasilitas-desa.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700' }}">
                    <i class="fas fa-building mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Fasilitas Desa</span>
                </a>
                @endcan

                @can('umkm.view')
                <a href="{{ route('umkm.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('umkm.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700' }}">
                    <i class="fas fa-store mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Data UMKM</span>
                </a>
                @endcan

                @can('transparansi-desa.view')
                <a href="{{ route('transparansi-desa.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('transparansi-desa.*') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg shadow-blue-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:shadow-md hover:text-blue-700' }}">
                    <i class="fas fa-chart-line mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Transparansi Desa</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Web Desa -->
        <div x-data="{ open: {{ request()->routeIs('berita.*') || request()->routeIs('testimoni.*') || request()->routeIs('web-desa.settings') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:shadow-md hover:text-orange-700">
                <div class="flex items-center">
                    <i class="fas fa-globe mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Web Desa</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('berita.view')
                <a href="{{ route('berita.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('berita.*') ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-lg shadow-orange-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:shadow-md hover:text-orange-700' }}">
                    <i class="fas fa-newspaper mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Berita & Pengumuman</span>
                </a>
                @endcan



                @can('testimoni.view')
                <a href="{{ route('testimoni.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('testimoni.*') ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-lg shadow-orange-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:shadow-md hover:text-orange-700' }}">
                    <i class="fas fa-star mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Testimoni Warga</span>
                </a>
                @endcan

                <a href="{{ route('web-desa.settings') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('web-desa.settings') ? 'bg-gradient-to-r from-orange-600 to-orange-700 text-white shadow-lg shadow-orange-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100 hover:shadow-md hover:text-orange-700' }}">
                    <i class="fas fa-cog mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Pengaturan Web</span>
                </a>

            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200/50 my-4"></div>

        <!-- Laporan & Analisis -->
        <div x-data="{ open: {{ request()->routeIs('laporan.*') || request()->routeIs('statistics.*') || request()->routeIs('comparison.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 hover:shadow-md hover:text-indigo-700">
                <div class="flex items-center">
                    <i class="fas fa-chart-line mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Laporan & Analisis</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('laporan.view')
                <a href="{{ route('laporan.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('laporan.*') ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg shadow-indigo-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 hover:shadow-md hover:text-indigo-700' }}">
                    <i class="fas fa-file-alt mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Laporan</span>
                </a>
                @endcan

                @can('statistics.view')
                <a href="{{ route('statistics.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('statistics.*') ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg shadow-indigo-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 hover:shadow-md hover:text-indigo-700' }}">
                    <i class="fas fa-chart-bar mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Statistik</span>
                </a>
                @endcan

                @can('statistics.view')
                <a href="{{ route('comparison.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('comparison.*') ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-lg shadow-indigo-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 hover:shadow-md hover:text-indigo-700' }}">
                    <i class="fas fa-balance-scale mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Perbandingan</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200/50 my-4"></div>

        <!-- Data Management -->
        <div x-data="{ open: {{ request()->routeIs('import.*') || request()->routeIs('export-import.*') || request()->routeIs('export.*') || request()->routeIs('backup.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-cyan-100 hover:shadow-md hover:text-cyan-700">
                <div class="flex items-center">
                    <i class="fas fa-database mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Data Management</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('penduduk.import')
                <a href="{{ route('import.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('import.*') ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 text-white shadow-lg shadow-cyan-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-cyan-100 hover:shadow-md hover:text-cyan-700' }}">
                    <i class="fas fa-file-import mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Import Data</span>
                </a>
                @endcan

                @can('export.view')
                <a href="{{ route('export-import.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('export-import.*') || request()->routeIs('export.*') ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 text-white shadow-lg shadow-cyan-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-cyan-100 hover:shadow-md hover:text-cyan-700' }}">
                    <i class="fas fa-download mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Export Data</span>
                </a>
                @endcan

                @can('backup.manage')
                <a href="{{ route('backup.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('backup.*') ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 text-white shadow-lg shadow-cyan-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-cyan-100 hover:shadow-md hover:text-cyan-700' }}">
                    <i class="fas fa-database mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Backup</span>
                </a>
                @endcan


            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200/50 my-4"></div>

        <!-- Admin & Monitoring -->
        <div x-data="{ open: {{ request()->routeIs('audit-log.*') || request()->routeIs('settings.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-3 rounded-2xl transition-all duration-300 text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt mr-3 text-lg"></i>
                    <span class="font-semibold text-base">Admin & Monitoring</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 mt-2 space-y-1">
                @can('audit_log.view')
                <a href="{{ route('audit-log.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('audit-log.*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700' }}">
                    <i class="fas fa-history mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Audit Log</span>
                </a>
                @endcan

                @can('settings.view')
                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('settings.index') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700' }}">
                    <i class="fas fa-cog mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Pengaturan</span>
                </a>
                <a href="{{ route('settings.wilayah.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('settings.wilayah.index') || request()->routeIs('settings.wilayah.rt.*') || request()->routeIs('settings.wilayah.change-log.*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700' }}">
                    <i class="fas fa-map-marked-alt mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Master Wilayah</span>
                </a>
                <a href="{{ route('settings.wilayah.import-conflicts.index') }}" class="flex items-center px-4 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('settings.wilayah.import-conflicts.*') ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg shadow-red-200' : 'text-gray-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:shadow-md hover:text-red-700' }}">
                    <i class="fas fa-exclamation-triangle mr-3 text-sm"></i>
                    <span class="text-sm font-medium">Import Issue Queue</span>
                </a>
                @endcan
            </div>
        </div>
    </nav>
</div>
