@extends('layouts.app')

@section('title', 'KK Bermasalah - ' . $nkk)
@section('subtitle', 'Selesaikan status Kartu Keluarga yang kehilangan Kepala Keluarga')

@section('content')
<div class="space-y-6">

    {{-- ===== HEADER ===== --}}
    <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">KK Bermasalah</h1>
                    <p class="text-red-100 text-sm sm:text-base font-mono">NKK: {{ $nkk }}</p>
                    <p class="text-red-200 text-sm">KK kehilangan Kepala Keluarga — perlu diselesaikan</p>
                </div>
            </div>
            <a href="{{ route('kartu-keluarga.show', $nkk) }}"
               class="flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl transition-all duration-200 self-start sm:self-auto">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail KK
            </a>
        </div>
    </div>

    {{-- ===== PROGRESS STEPPER ===== --}}
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between max-w-lg mx-auto">
            {{-- Step 1 --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $kkRecord->status_kk === 'bermasalah' ? 'bg-red-500 text-white ring-4 ring-red-200' :
                       ($kkRecord->status_kk === 'resolved' ? 'bg-green-500 text-white' : 'bg-green-500 text-white') }}">
                    @if($kkRecord->status_kk === 'bermasalah') 1
                    @else <i class="fas fa-check text-xs"></i>
                    @endif
                </div>
                <span class="text-xs font-medium mt-2 text-center
                    {{ $kkRecord->status_kk === 'bermasalah' ? 'text-red-600' : 'text-green-600' }}">
                    Tunjuk KK<br>Sementara
                </span>
            </div>
            {{-- Connector --}}
            <div class="flex-1 h-1 mx-3 rounded
                {{ in_array($kkRecord->status_kk, ['bermasalah_sementara','resolved']) ? 'bg-green-400' : 'bg-gray-200' }}"></div>
            {{-- Step 2 --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $kkRecord->status_kk === 'bermasalah_sementara' ? 'bg-orange-500 text-white ring-4 ring-orange-200' :
                       ($kkRecord->status_kk === 'resolved' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                    @if($kkRecord->status_kk === 'resolved') <i class="fas fa-check text-xs"></i>
                    @else 2
                    @endif
                </div>
                <span class="text-xs font-medium mt-2 text-center
                    {{ $kkRecord->status_kk === 'bermasalah_sementara' ? 'text-orange-600' :
                       ($kkRecord->status_kk === 'resolved' ? 'text-green-600' : 'text-gray-400') }}">
                    Input NKK<br>Baru Permanen
                </span>
            </div>
            {{-- Connector --}}
            <div class="flex-1 h-1 mx-3 rounded
                {{ $kkRecord->status_kk === 'resolved' ? 'bg-green-400' : 'bg-gray-200' }}"></div>
            {{-- Step 3 --}}
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                    {{ $kkRecord->status_kk === 'resolved' ? 'bg-green-500 text-white ring-4 ring-green-200' : 'bg-gray-200 text-gray-400' }}">
                    @if($kkRecord->status_kk === 'resolved') <i class="fas fa-check text-xs"></i>
                    @else 3
                    @endif
                </div>
                <span class="text-xs font-medium mt-2 text-center
                    {{ $kkRecord->status_kk === 'resolved' ? 'text-green-600' : 'text-gray-400' }}">
                    Selesai
                </span>
            </div>
        </div>
    </div>

    {{-- ===== INFO PENYEBAB ===== --}}
    @if($mutasiPenyebab)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-4">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-red-600"></i>
        </div>
        <div>
            <p class="font-semibold text-red-800">Penyebab: {{ $mutasiPenyebab->jenis_mutasi_label }}</p>
            <p class="text-sm text-red-700 mt-1">
                Penduduk: <strong>{{ $mutasiPenyebab->penduduk?->nama ?? '-' }}</strong> &mdash;
                Tanggal: {{ $mutasiPenyebab->tanggal_mutasi?->format('d M Y') ?? '-' }}
            </p>
            @if($kkRecord->kk_bermasalah_sejak)
            <p class="text-xs text-red-500 mt-1">
                <i class="fas fa-clock mr-1"></i>
                Bermasalah sejak {{ $kkRecord->kk_bermasalah_sejak->format('d M Y H:i') }}
                ({{ $kkRecord->harisBermasalah() }} hari)
            </p>
            @endif
        </div>
    </div>
    @endif

    {{-- ===== STEP 1: TUNJUK KK SEMENTARA ===== --}}
    @if($kkRecord->status_kk === 'bermasalah')
    <div class="bg-white rounded-2xl shadow-lg border-l-4 border-red-500 p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-plus text-red-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Langkah 1 — Tunjuk Kepala Keluarga Sementara</h2>
                <p class="text-sm text-gray-500">Pilih anggota keluarga aktif untuk diangkat sementara. NKK belum berubah.</p>
            </div>
        </div>

        @if($anggotaAktif->isEmpty())
        <div class="bg-gray-50 rounded-xl p-8 text-center text-gray-500">
            <i class="fas fa-users-slash text-3xl mb-3"></i>
            <p class="font-medium">Tidak ada anggota aktif yang bisa dipilih.</p>
            <p class="text-sm mt-1">Semua anggota sudah dimutasi atau KK ini sudah kosong.</p>
        </div>
        @else
        <form action="{{ route('kk.resolve.sementara', $nkk) }}" method="POST" id="form-sementara">
            @csrf
            <div class="space-y-3 mb-6">
                @foreach($anggotaAktif as $anggota)
                <label class="flex items-center gap-4 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                    <input type="radio" name="kandidat_id" value="{{ $anggota->id }}"
                           class="w-5 h-5 text-orange-500 accent-orange-500" required>
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $anggota->nama }}</p>
                        <p class="text-sm text-gray-500">NIK: {{ $anggota->nik }} &bull; {{ $anggota->kedudukan_keluarga }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                        {{ $anggota->jenis_kelamin === 'L' ? '♂' : '♀' }}
                        {{ $anggota->tempat_lahir ?? '' }}
                    </span>
                </label>
                @endforeach
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 text-sm text-amber-800">
                <i class="fas fa-lightbulb mr-2 text-amber-500"></i>
                Penunjukan ini bersifat sementara. Mutasi asal masih bisa di-undo selama KK belum diselesaikan permanen.
            </div>
            <button type="button" onclick="konfirmasiSementara()"
                    class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] font-semibold">
                <i class="fas fa-user-check mr-2"></i> Tunjuk sebagai KK Sementara
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- ===== STEP 2: SELESAIKAN PERMANEN ===== --}}
    @if($kkRecord->status_kk === 'bermasalah_sementara')
    <div class="bg-white rounded-2xl shadow-lg border-l-4 border-orange-500 p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-id-card text-orange-600"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Langkah 2 — Input NKK Baru dari Disdukcapil</h2>
                <p class="text-sm text-gray-500">Masukkan NKK baru yang sudah diterbitkan resmi. Tindakan ini bersifat <strong>permanen dan tidak bisa di-undo</strong>.</p>
            </div>
        </div>

        {{-- Info KK Sementara aktif --}}
        @if($kkSementara)
        <div class="flex items-center gap-3 bg-orange-50 border border-orange-200 rounded-xl p-4 mb-5">
            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-orange-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-orange-800">KK Sementara aktif: {{ $kkSementara->nama }}</p>
                <p class="text-xs text-orange-600">NIK: {{ $kkSementara->nik }} &bull; {{ $kkSementara->kedudukan_keluarga }}</p>
            </div>
            <form action="{{ route('kk.batalkan.sementara', $nkk) }}" method="POST" class="ml-auto">
                @csrf
                <button type="button" onclick="konfirmaBatalSementara()"
                        class="text-sm text-red-600 hover:text-red-800 underline">
                    <i class="fas fa-undo mr-1"></i>Batalkan pilihan
                </button>
            </form>
            <form action="{{ route('kk.batalkan.sementara', $nkk) }}" method="POST" id="form-batalkan" class="hidden">@csrf</form>
        </div>
        @endif

        <form action="{{ route('kk.resolve.permanen', $nkk) }}" method="POST" id="form-permanen">
            @csrf
            <div class="mb-5">
                <label for="nkk_baru" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor Kartu Keluarga Baru <span class="text-red-500">*</span>
                </label>
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" name="nkk_baru" id="nkk_baru"
                           maxlength="16" pattern="[0-9]{16}"
                           placeholder="Masukkan 16 digit NKK baru..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm transition-colors"
                           required>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    NKK harus 16 digit angka dan belum pernah digunakan di sistem.
                </p>
                @error('nkk_baru')
                <p class="text-red-600 text-sm mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-800">
                <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>Perhatian — Tindakan Permanen:</p>
                <ul class="space-y-1 list-disc list-inside text-red-700">
                    <li>Seluruh anggota KK ini akan dipindahkan ke NKK baru</li>
                    <li>NKK lama ({{ $nkk }}) akan diarsipkan sebagai audit trail</li>
                    <li>Mutasi asal <strong>tidak bisa di-undo</strong> setelah ini</li>
                </ul>
            </div>

            <button type="button" onclick="konfirmasiPermanen()"
                    class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 hover:scale-[1.02] font-semibold">
                <i class="fas fa-check-circle mr-2"></i> Selesaikan Permanen
            </button>
        </form>
    </div>
    @endif

    {{-- ===== STATUS RESOLVED ===== --}}
    @if($kkRecord->status_kk === 'resolved')
    <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
        </div>
        <h2 class="text-xl font-bold text-green-800 mb-2">KK Berhasil Diselesaikan</h2>
        <p class="text-green-700 text-sm">NKK ini telah diarsipkan. Anggota keluarga sudah berpindah ke NKK baru.</p>
        <a href="{{ route('kartu-keluarga.index', ['status' => 'bermasalah']) }}"
           class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
            <i class="fas fa-list mr-2"></i> Lihat KK Bermasalah Lainnya
        </a>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script nonce="{{ $csp_nonce }}">
function konfirmasiSementara() {
    const selected = document.querySelector('input[name="kandidat_id"]:checked');
    if (!selected) {
        Swal.fire({ icon: 'warning', title: 'Pilih kandidat dulu!', text: 'Silakan pilih salah satu anggota keluarga.' });
        return;
    }
    const nama = selected.closest('label').querySelector('.font-semibold').textContent.trim();
    Swal.fire({
        title: 'Konfirmasi Penunjukan',
        html: `<p>Tunjuk <strong>${nama}</strong> sebagai Kepala Keluarga sementara?</p>
               <p class="text-sm text-gray-500 mt-2">Mutasi asal masih bisa di-undo selama belum diselesaikan permanen.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-user-check mr-1"></i> Ya, Tunjuk',
        cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) document.getElementById('form-sementara').submit(); });
}

function konfirmasiPermanen() {
    const nkkBaru = document.getElementById('nkk_baru').value.trim();
    if (!nkkBaru || nkkBaru.length !== 16 || !/^\d+$/.test(nkkBaru)) {
        Swal.fire({ icon: 'warning', title: 'NKK tidak valid!', text: 'Masukkan 16 digit angka NKK baru.' });
        return;
    }
    Swal.fire({
        title: '⚠️ Tindakan Permanen!',
        html: `<div class="text-left">
                 <p class="mb-3">NKK baru: <strong class="font-mono">${nkkBaru}</strong></p>
                 <p class="text-sm text-red-600">Seluruh anggota KK akan dipindahkan ke NKK baru ini dan mutasi asal tidak dapat di-undo setelah ini.</p>
               </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check-circle mr-1"></i> Ya, Selesaikan Permanen',
        cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) document.getElementById('form-permanen').submit(); });
}

function konfirmaBatalSementara() {
    Swal.fire({
        title: 'Batalkan KK Sementara?',
        text: 'Anggota yang ditunjuk akan dikembalikan ke kedudukan semula.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-undo mr-1"></i> Ya, Batalkan',
        cancelButtonText: 'Tidak'
    }).then(r => { if (r.isConfirmed) document.getElementById('form-batalkan').submit(); });
}

@if(session('success'))
    Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session("success") }}', timer: 3000, showConfirmButton: false });
@endif
@if(session('error'))
    Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session("error") }}' });
@endif
</script>
@endpush

