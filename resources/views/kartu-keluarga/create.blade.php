@extends('layouts.app')

@section('title', 'Tambah Kartu Keluarga')
@section('subtitle', 'Buat Kartu Keluarga baru')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-plus text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Tambah Kartu Keluarga</h1>
                    <p class="text-green-100 text-sm sm:text-base">Buat Kartu Keluarga baru dengan kepala keluarga</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kk.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <!-- Global Error Display -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Terjadi kesalahan validasi:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Data Kartu Keluarga</h3>
                <p class="text-sm text-gray-600">Lengkapi data Kartu Keluarga dengan benar</p>
            </div>
        </div>

        <form action="{{ route('kk.store') }}" method="POST" class="space-y-8">
                @csrf

                <!-- NKK -->
                <div class="mb-6">
                    <label for="nkk" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Kartu Keluarga (NKK) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nkk" id="nkk" value="{{ old('nkk') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nkk') border-red-500 @enderror"
                           placeholder="Masukkan 16 digit NKK"
                           maxlength="16" required>
                    @error('nkk')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data Kepala Keluarga -->
                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-crown text-yellow-500 mr-2"></i>
                        Data Kepala Keluarga
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- NIK Kepala Keluarga -->
                    <div>
                        <label for="nik_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            NIK Kepala Keluarga <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nik_kepala_keluarga" id="nik_kepala_keluarga" value="{{ old('nik_kepala_keluarga') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nik_kepala_keluarga') border-red-500 @enderror"
                               placeholder="Masukkan 16 digit NIK"
                               maxlength="16" required>
                        @error('nik_kepala_keluarga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Kepala Keluarga -->
                    <div>
                        <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga" value="{{ old('nama_kepala_keluarga') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_kepala_keluarga') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap" required>
                        @error('nama_kepala_keluarga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jenis_kelamin') border-red-500 @enderror" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tempat_lahir') border-red-500 @enderror"
                               placeholder="Masukkan tempat lahir" required>
                        @error('tempat_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_lahir') border-red-500 @enderror" required>
                        @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">
                            Agama <span class="text-red-500">*</span>
                        </label>
                        <select name="agama" id="agama"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('agama') border-red-500 @enderror" required>
                            <option value="">Pilih Agama</option>
                            <option value="Islam" {{ old('agama') === 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') === 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('agama') === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                        @error('agama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Perkawinan -->
                    <div>
                        <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Perkawinan <span class="text-red-500">*</span>
                        </label>
                        <select name="status_perkawinan" id="status_perkawinan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status_perkawinan') border-red-500 @enderror" required>
                            <option value="">Pilih Status Perkawinan</option>
                            <option value="Belum Kawin" {{ old('status_perkawinan') === 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ old('status_perkawinan') === 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Cerai Hidup" {{ old('status_perkawinan') === 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                            <option value="Cerai Mati" {{ old('status_perkawinan') === 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                        </select>
                        @error('status_perkawinan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pekerjaan -->
                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pekerjaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pekerjaan') border-red-500 @enderror"
                               placeholder="Masukkan pekerjaan" required>
                        @error('pekerjaan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pendidikan -->
                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">
                            Pendidikan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="pendidikan" id="pendidikan" value="{{ old('pendidikan') }}"
                               placeholder="Contoh: SD, SMP, SMA, S1, dll" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('pendidikan') border-red-500 @enderror">
                        @error('pendidikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Ayah -->
                    <div>
                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Ayah
                        </label>
                        <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah') }}"
                               placeholder="Masukkan nama ayah"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_ayah') border-red-500 @enderror">
                        @error('nama_ayah')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Ibu -->
                    <div>
                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Ibu
                        </label>
                        <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu') }}"
                               placeholder="Masukkan nama ibu"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_ibu') border-red-500 @enderror">
                        @error('nama_ibu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                        Alamat
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" id="alamat" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror"
                                  placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RW Master -->
                    <div>
                        <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-2">
                            RW Master <span class="text-red-500">*</span>
                        </label>
                        <select id="rw_id" name="rw_id" onchange="populateRtByRw()" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rw_id') border-red-500 @enderror">
                            <option value="">Pilih RW</option>
                            @foreach($masterRwOptions as $rw)
                                <option value="{{ $rw['id'] }}" {{ old('rw_id') == $rw['id'] ? 'selected' : '' }}>RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RT Master -->
                    <div>
                        <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">
                            RT Master <span class="text-red-500">*</span>
                        </label>
                        <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rt_id') border-red-500 @enderror">
                            <option value="">Pilih RT</option>
                        </select>
                        @error('rt_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dusun (Auto-filled) -->
                    <div class="md:col-span-2">
                        <label for="dusun_display" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                        <input type="text" id="dusun_display" disabled
                               class="w-full px-3 py-2 border border-gray-100 bg-gray-50 rounded-lg text-gray-500"
                               placeholder="Otomatis dari RT">
                        <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id') }}">
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('keterangan') border-red-500 @enderror resize-none"
                                  placeholder="Catatan atau keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('kk.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Kartu Keluarga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@noncescript
@noncescript
const masterRwOptions = @json($masterRwOptions);

function populateRtByRw(initial = false) {
    const rwId = document.getElementById('rw_id').value;
    const rtSelect = document.getElementById('rt_id');
    rtSelect.innerHTML = '<option value="">Pilih RT</option>';

    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    if (rwObj) {
        rwObj.rts.forEach(rt => {
            const opt = document.createElement('option');
            opt.value = rt.id;
            opt.textContent = `RT ${rt.kode}${rt.dusun ? ' - ' + rt.dusun : ''}`;
            rtSelect.appendChild(opt);
        });
    }
    syncDusunFromRt();
}

function syncDusunFromRt() {
    const rwId = document.getElementById('rw_id').value;
    const rtId = document.getElementById('rt_id').value;
    const dusunDisplay = document.getElementById('dusun_display');
    const dusunHidden = document.getElementById('dusun_id');

    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    const rtObj = rwObj?.rts?.find(r => String(r.id) === String(rtId));

    if (rtObj) {
        dusunDisplay.value = rtObj.dusun || 'N/A';
        dusunHidden.value = rtObj.dusun_id || '';
    } else {
        dusunDisplay.value = '';
        dusunHidden.value = '';
    }
}

// Format NKK dan NIK input
document.getElementById('nkk').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

document.getElementById('nik_kepala_keluarga').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
@endnoncescript
@endnoncescript
@endpush
@endsection

