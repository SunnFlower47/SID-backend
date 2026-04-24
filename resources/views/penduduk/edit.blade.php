
@extends('layouts.app')

@section('title', 'Edit Penduduk')
@section('subtitle', 'Ubah data penduduk')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-300 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Penduduk</h1>
                    <p class="text-green-100 text-sm sm:text-base">Ubah data {{ $penduduk->nama }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                @if($penduduk->nkk)
                    <a href="{{ route('penduduk.family.address.form', $penduduk->nkk) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-home mr-2"></i>
                        <span class="hidden sm:inline">Update Alamat Keluarga</span>
                        <span class="sm:hidden">Update Alamat</span>
                    </a>
                @endif
                <a href="{{ route('penduduk.show', $penduduk) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-eye mr-2"></i>
                    <span class="hidden sm:inline">Lihat Detail</span>
                    <span class="sm:hidden">Detail</span>
                </a>
                <a href="{{ route('penduduk.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Family Info Card -->
    @if($penduduk->nkk)
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg border border-green-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-home text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Informasi Keluarga</h3>
                        <p class="text-sm text-gray-600">No KK: <span class="font-mono font-semibold">{{ $penduduk->nkk }}</span></p>
                        <p class="text-sm text-gray-600">Kedudukan: <span class="font-semibold text-green-700">{{ $penduduk->kedudukan_keluarga }}</span></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="text-sm text-gray-500 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200 w-full sm:w-auto text-center sm:text-left">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span class="hidden sm:inline">Gunakan tombol di atas untuk update alamat</span>
                        <span class="sm:hidden">Gunakan tombol di atas</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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

        <form method="POST" action="{{ route('penduduk.update', $penduduk) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Data Pribadi -->
            <div></div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                        <div class="relative">
                            <input type="text" name="nik" id="nik" value="{{ old('nik', $penduduk->nik) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik') border-red-500 @enderror"
                                   required maxlength="16">
                            <div id="nik_check_loading" class="absolute right-3 top-3 hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        <div id="nikStatusInfo" class="mt-2 text-sm" style="display: none;">
                            <div id="nikNewInfo" class="text-green-700 bg-green-50 p-2 rounded-lg border border-green-200" style="display: none;">
                                <i class="fas fa-check-circle mr-1"></i>
                                <strong>NIK Tersedia:</strong> NIK ini belum terdaftar di database
                            </div>
                            <div id="nikExistingInfo" class="text-red-700 bg-red-50 p-2 rounded-lg border border-red-200" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>NIK Sudah Ada:</strong> <span id="existingNIKDetails"></span>
                            </div>
                        </div>
                        @error('nik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('nik'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('nik') }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">Format: 16 digit angka</p>
                    </div>

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $penduduk->nama) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror"
                               required>
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('nama'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('nama') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kelamin') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="LAKI-LAKI" {{ old('jenis_kelamin', $penduduk->jenis_kelamin) == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="PEREMPUAN" {{ old('jenis_kelamin', $penduduk->jenis_kelamin) == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('jenis_kelamin'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('jenis_kelamin') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir *</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $penduduk->tempat_lahir) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tempat_lahir') border-red-500 @enderror"
                               required>
                        @error('tempat_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('tempat_lahir'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('tempat_lahir') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $penduduk->tanggal_lahir ? $penduduk->tanggal_lahir->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-500 @enderror"
                               required>
                        @error('tanggal_lahir')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('tanggal_lahir'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('tanggal_lahir') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="agama" class="block text-sm font-medium text-gray-700 mb-2">Agama *</label>
                        <input type="text" name="agama" id="agama" value="{{ old('agama', $penduduk->agama) }}"
                               placeholder="Contoh: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('agama') border-red-500 @enderror"
                               required>
                        @error('agama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('agama'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('agama') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="status_perkawinan" class="block text-sm font-medium text-gray-700 mb-2">Status Perkawinan *</label>
                        <input type="text" name="status_perkawinan" id="status_perkawinan" value="{{ old('status_perkawinan', $penduduk->status_perkawinan) }}"
                               placeholder="Contoh: Belum Kawin, Kawin, Cerai Hidup, Cerai Mati"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status_perkawinan') border-red-500 @enderror"
                               required>
                        @error('status_perkawinan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('status_perkawinan'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('status_perkawinan') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="kedudukan_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Kedudukan dalam Keluarga *</label>
                        <input type="text" name="kedudukan_keluarga" id="kedudukan_keluarga" value="{{ old('kedudukan_keluarga', $penduduk->kedudukan_keluarga) }}"
                               placeholder="Contoh: Kepala Keluarga, Istri, Anak, Orang Tua, Lainnya"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kedudukan_keluarga') border-red-500 @enderror"
                               required>
                        @error('kedudukan_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('kedudukan_keluarga'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('kedudukan_keluarga') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Orang Tua</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_ayah" class="block text-sm font-medium text-gray-700 mb-2">Nama Ayah</label>
                        <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah', $penduduk->nama_ayah) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_ayah') border-red-500 @enderror">
                        @error('nama_ayah')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('nama_ayah'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('nama_ayah') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="nama_ibu" class="block text-sm font-medium text-gray-700 mb-2">Nama Ibu</label>
                        <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu', $penduduk->nama_ibu) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_ibu') border-red-500 @enderror">
                        @error('nama_ibu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('nama_ibu'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('nama_ibu') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pendidikan & Pekerjaan -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pendidikan & Pekerjaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="pendidikan" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                        <input type="text" id="pendidikan" name="pendidikan" value="{{ old('pendidikan', $penduduk->pendidikan) }}"
                               placeholder="Contoh: SD, SMP, SMA, S1, dll"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pendidikan') border-red-500 @enderror">
                        @error('pendidikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('pendidikan'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('pendidikan') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan *</label>
                        <input type="text" name="pekerjaan" id="pekerjaan" value="{{ old('pekerjaan', $penduduk->pekerjaan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pekerjaan') border-red-500 @enderror"
                               required>
                        @error('pekerjaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('pekerjaan'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('pekerjaan') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Alamat</h3>
                    @if($penduduk->nkk)
                        <div class="text-sm text-gray-500 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200 hidden sm:block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Untuk update alamat seluruh keluarga, gunakan tombol "Update Alamat Keluarga" di atas
                        </div>
                    @endif
                </div>
                @if($penduduk->nkk)
                    <div class="md:col-span-2 sm:hidden mb-4">
                        <div class="text-sm text-gray-500 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
                            <i class="fas fa-info-circle mr-1"></i>
                            Untuk update alamat seluruh keluarga, gunakan tombol "Update Alamat" di atas
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                        <textarea name="alamat" id="alamat" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-500 @enderror"
                                  required>{{ old('alamat', $penduduk->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('alamat'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('alamat') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-2">RW Master *</label>
                        <select id="rw_id" name="rw_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rw_id') border-red-500 @enderror">
                            <option value="">Pilih RW</option>
                            @foreach(($rws ?? collect()) as $rw)
                                <option value="{{ $rw->id }}" {{ (string)old('rw_id', optional($penduduk)->rw_id) === (string)$rw->id ? 'selected' : '' }}>RW {{ $rw->kode }} - {{ $rw->nama }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">RT Master *</label>
                        <select id="rt_id" name="rt_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rt_id') border-red-500 @enderror">
                            <option value="">Pilih RT</option>
                        </select>
                        @error('rt_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW (kode)</label>
                        <input type="text" name="rw" id="rw" value="{{ old('rw', $penduduk->rw) }}" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed @error('rw') border-red-500 @enderror">
                        @error('rw')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT (kode)</label>
                        <input type="text" name="rt" id="rt" value="{{ old('rt', $penduduk->rt) }}" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed @error('rt') border-red-500 @enderror">
                        @error('rt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                        <input type="text" name="dusun" id="dusun" value="{{ old('dusun', $penduduk->dusun) }}" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed @error('dusun') border-red-500 @enderror">
                        @error('dusun')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $penduduk->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($errors->has('keterangan'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('keterangan') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md order-1">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
                <!-- Tombol Update Alamat Keluarga sudah ada di header, tidak perlu duplikasi -->
                <a href="{{ route('penduduk.show', $penduduk) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md order-3">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script nonce="{{ $csp_nonce }}">
const masterRwOptions = @json($masterRwOptions ?? []);

function syncMasterWilayahFromSelection() {
    const rwId = document.getElementById('rw_id')?.value;
    const rtId = document.getElementById('rt_id')?.value;
    const rwInput = document.getElementById('rw');
    const rtInput = document.getElementById('rt');
    const dusunInput = document.getElementById('dusun');

    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    const rtObj = rwObj?.rts?.find(r => String(r.id) === String(rtId));

    if (rwInput) rwInput.value = rwObj?.kode || '';
    if (rtInput) rtInput.value = rtObj?.kode || '';
    if (dusunInput) dusunInput.value = rtObj?.dusun || '';
}

function populateRtByRw() {
    const rwId = document.getElementById('rw_id')?.value;
    const rtSelect = document.getElementById('rt_id');
    if (!rtSelect) return;

    rtSelect.innerHTML = '<option value="">Pilih RT</option>';
    const rwObj = masterRwOptions.find(r => String(r.id) === String(rwId));
    (rwObj?.rts || []).forEach(rt => {
        const opt = document.createElement('option');
        opt.value = rt.id;
        opt.textContent = `RT ${rt.kode}${rt.dusun ? ` - ${rt.dusun}` : ''}`;
        rtSelect.appendChild(opt);
    });
}

// NIK Check functions
function checkNIKExists(nik) {
    const loading = document.getElementById('nik_check_loading');
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');

    if (loading) loading.classList.remove('hidden');
    if (statusInfo) statusInfo.style.display = 'none';

    fetch(`{{ route('penduduk.check-nik') }}?nik=${encodeURIComponent(nik)}&exclude_id={{ $penduduk->id }}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        cache: 'no-cache'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (loading) loading.classList.add('hidden');

        if (data.exists) {
            // NIK already exists
            showExistingNIKInfo(data.data);
        } else {
            // NIK is new
            showNewNIKInfo();
        }
    })
    .catch(error => {
        console.error('NIK check error:', error);
        if (loading) loading.classList.add('hidden');
        hideNIKStatusInfo();
    });
}

function showNewNIKInfo() {
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'block';
    if (existingInfo) existingInfo.style.display = 'none';
}

function showExistingNIKInfo(data) {
    const statusInfo = document.getElementById('nikStatusInfo');
    const newInfo = document.getElementById('nikNewInfo');
    const existingInfo = document.getElementById('nikExistingInfo');
    const existingDetails = document.getElementById('existingNIKDetails');

    if (statusInfo) statusInfo.style.display = 'block';
    if (newInfo) newInfo.style.display = 'none';
    if (existingInfo) existingInfo.style.display = 'block';
    if (existingDetails) {
        existingDetails.textContent = `${data.nama} - No KK: ${data.nkk} - RT ${data.rt}/RW ${data.rw}`;
    }
}

function hideNIKStatusInfo() {
    const statusInfo = document.getElementById('nikStatusInfo');
    if (statusInfo) statusInfo.style.display = 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show validation errors with SweetAlert if any
    @if($errors->any())
        Swal.fire({
            title: 'Terjadi Kesalahan Validasi!',
            html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444'
        });
    @endif

    // Add event listener for NIK input
    const nikInput = document.getElementById('nik');
    if (nikInput) {
        let nikCheckTimeout;
        nikInput.addEventListener('input', function() {
            clearTimeout(nikCheckTimeout);
            const nikValue = this.value.trim();

            if (nikValue.length === 16) {
                nikCheckTimeout = setTimeout(() => {
                    checkNIKExists(nikValue);
                }, 500); // 500ms debounce
            } else {
                hideNIKStatusInfo();
            }
        });
    }

    const rwMaster = document.getElementById('rw_id');
    const rtMaster = document.getElementById('rt_id');
    const oldRwId = @json(old('rw_id'));
    const oldRtId = @json(old('rt_id'));

    if (rwMaster) {
        if (oldRwId) {
            rwMaster.value = String(oldRwId);
        } else {
            const currentRwCode = String(@json($penduduk->rw ?? '')).padStart(3, '0');
            const matchedRw = masterRwOptions.find(r => String(r.kode).padStart(3, '0') === currentRwCode);
            if (matchedRw) rwMaster.value = String(matchedRw.id);
        }

        populateRtByRw();

        if (rtMaster) {
            if (oldRtId) {
                rtMaster.value = String(oldRtId);
            } else {
                const rwObj = masterRwOptions.find(r => String(r.id) === String(rwMaster.value));
                const currentRtCode = String(@json($penduduk->rt ?? '')).padStart(3, '0');
                const matchedRt = (rwObj?.rts || []).find(rt => String(rt.kode).padStart(3, '0') === currentRtCode);
                if (matchedRt) rtMaster.value = String(matchedRt.id);
            }
        }

        syncMasterWilayahFromSelection();
        rwMaster.addEventListener('change', function () {
            populateRtByRw();
            syncMasterWilayahFromSelection();
        });
        rtMaster?.addEventListener('change', syncMasterWilayahFromSelection);
    }

    // Add form submission handler to show loading and prevent double submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Perubahan';
                }, 10000);
            }
        });
    }

    // Fallback error display for hosting issues
    function displayValidationErrors(errors) {
        // Clear existing error messages
        document.querySelectorAll('.validation-error').forEach(el => el.remove());

        // Display new error messages
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                // Add red border
                input.classList.add('border-red-500');

                // Add error message below input
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error mt-1 text-sm text-red-600';
                errorDiv.textContent = errors[field][0];

                // Insert after input
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
        });
    }

    // Check for validation errors in URL parameters (fallback for hosting)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('validation_errors')) {
        try {
            const errors = JSON.parse(decodeURIComponent(urlParams.get('validation_errors')));
            displayValidationErrors(errors);
        } catch (e) {
            console.log('Could not parse validation errors from URL');
        }
    }
});
</script>
@endsection
