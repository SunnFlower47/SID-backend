@extends('layouts.app')

@section('title', 'Edit Data UMKM')
@section('subtitle', 'Edit data Usaha Mikro, Kecil, dan Menengah')

@section('content')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Data UMKM</h1>
            <p class="text-gray-600 mt-1">Edit data Usaha Mikro, Kecil, dan Menengah</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('umkm.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center transition-colors shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('umkm.update', $umkm) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Nama Usaha -->
                <div class="lg:col-span-2">
                    <label for="nama_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Usaha <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_usaha" id="nama_usaha" value="{{ old('nama_usaha', $umkm->nama_usaha) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_usaha') border-red-500 @enderror"
                           placeholder="Masukkan nama usaha">
                    @error('nama_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nama Pemilik -->
                <div>
                    <label for="nama_pemilik" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pemilik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pemilik" id="nama_pemilik" value="{{ old('nama_pemilik', $umkm->nama_pemilik) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nama_pemilik') border-red-500 @enderror"
                           placeholder="Masukkan nama pemilik">
                    @error('nama_pemilik')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- NIK Pemilik -->
                <div>
                    <label for="nik_pemilik" class="block text-sm font-medium text-gray-700 mb-2">NIK Pemilik</label>
                    <input type="text" name="nik_pemilik" id="nik_pemilik" value="{{ old('nik_pemilik', $umkm->nik_pemilik) }}" maxlength="16"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('nik_pemilik') border-red-500 @enderror"
                           placeholder="16 digit NIK">
                    @error('nik_pemilik')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Alamat Usaha -->
                <div class="lg:col-span-2">
                    <label for="alamat_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Usaha <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat_usaha" id="alamat_usaha" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('alamat_usaha') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap usaha">{{ old('alamat_usaha', $umkm->alamat_usaha) }}</textarea>
                    @error('alamat_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- RW Master -->
                <div>
                    <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-2">RW Master <span class="text-red-500">*</span></label>
                    <select id="rw_id" name="rw_id" onchange="populateRtByRw()" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('rw_id') border-red-500 @enderror">
                        <option value="">Pilih RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $umkm->rw_id) == $rw->id ? 'selected' : '' }}>RW {{ $rw->kode }} - {{ $rw->nama }}</option>
                        @endforeach
                    </select>
                    @error('rw_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- RT Master -->
                <div>
                    <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-2">
                        RT Master <span class="text-red-500">*</span>
                        @if(!$umkm->rt_id)
                            <span class="text-red-600 text-xs font-bold animate-pulse">(DATA TIDAK VALID)</span>
                        @endif
                    </label>
                    <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                            class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @if(!$umkm->rt_id) border-red-500 bg-red-50 @else border-gray-300 @endif @error('rt_id') border-red-500 @enderror">
                        <option value="">Pilih RT</option>
                    </select>
                    @error('rt_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}</p>
                    @enderror
                    @if(!$umkm->rt_id)
                        <p class="mt-1 text-xs text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i>RT Master belum terpetakan. Silakan pilih kembali.</p>
                    @endif
                </div>

                <!-- Dusun (Auto-filled) -->
                <div>
                    <label for="dusun_display" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                    <input type="text" id="dusun_display" value="{{ $umkm->dusun_label }}" disabled
                           class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-gray-500"
                           placeholder="Otomatis dari RT">
                    <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id', $umkm->dusun_id) }}">
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">No Telepon</label>
                    <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $umkm->no_telepon) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('no_telepon') border-red-500 @enderror"
                           placeholder="Nomor telepon">
                    @error('no_telepon')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $umkm->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="Email usaha">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Jenis Usaha -->
                <div>
                    <label for="jenis_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Usaha <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_usaha" id="jenis_usaha" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jenis_usaha') border-red-500 @enderror">
                        <option value="">Pilih Jenis Usaha</option>
                        <option value="makanan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="minuman" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'minuman' ? 'selected' : '' }}>Minuman</option>
                        <option value="kerajinan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                        <option value="jasa" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="perdagangan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                        <option value="pertanian" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'pertanian' ? 'selected' : '' }}>Pertanian</option>
                        <option value="peternakan" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'peternakan' ? 'selected' : '' }}>Peternakan</option>
                        <option value="lainnya" {{ old('jenis_usaha', $umkm->jenis_usaha) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Usaha -->
                <div>
                    <label for="status_usaha" class="block text-sm font-medium text-gray-700 mb-2">
                        Status Usaha <span class="text-red-500">*</span>
                    </label>
                    <select name="status_usaha" id="status_usaha" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('status_usaha') border-red-500 @enderror">
                        <option value="">Pilih Status Usaha</option>
                        <option value="aktif" {{ old('status_usaha', $umkm->status_usaha) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tutup" {{ old('status_usaha', $umkm->status_usaha) == 'tutup' ? 'selected' : '' }}>Tutup</option>
                        <option value="pindah" {{ old('status_usaha', $umkm->status_usaha) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    </select>
                    @error('status_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Tanggal Berdiri -->
                <div>
                    <label for="tanggal_berdiri" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berdiri</label>
                    <input type="date" name="tanggal_berdiri" id="tanggal_berdiri" value="{{ old('tanggal_berdiri', $umkm->tanggal_berdiri) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('tanggal_berdiri') border-red-500 @enderror">
                    @error('tanggal_berdiri')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                <!-- Jumlah Karyawan -->
                <div>
                    <label for="jumlah_karyawan" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Karyawan <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_karyawan" id="jumlah_karyawan" value="{{ old('jumlah_karyawan', $umkm->jumlah_karyawan) }}" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('jumlah_karyawan') border-red-500 @enderror"
                           placeholder="Jumlah karyawan">
                    @error('jumlah_karyawan')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $umkm->latitude) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('latitude') border-red-500 @enderror"
                           placeholder="Latitude">
                    @error('latitude')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $umkm->longitude) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('longitude') border-red-500 @enderror"
                           placeholder="Longitude">
                    @error('longitude')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Deskripsi Usaha -->
                <div class="lg:col-span-2">
                    <label for="deskripsi_usaha" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Usaha</label>
                    <textarea name="deskripsi_usaha" id="deskripsi_usaha" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('deskripsi_usaha') border-red-500 @enderror"
                              placeholder="Deskripsi usaha">{{ old('deskripsi_usaha', $umkm->deskripsi_usaha) }}</textarea>
                    @error('deskripsi_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Produk Unggulan -->
                <div class="lg:col-span-2">
                    <label for="produk_unggulan" class="block text-sm font-medium text-gray-700 mb-2">Produk Unggulan</label>
                    <div id="produk-container">
                        @if($umkm->produk_unggulan && is_array($umkm->produk_unggulan))
                            @foreach($umkm->produk_unggulan as $index => $produk)
                            <div class="flex space-x-2 mb-2">
                                <input type="text" name="produk_unggulan[]" value="{{ $produk }}" placeholder="Nama produk unggulan"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @if($index > 0)
                                <button type="button" onclick="removeProduk(this)" class="px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button type="button" onclick="addProduk()" class="px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600">
                                    <i class="fas fa-plus"></i>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        @else
                        <div class="flex space-x-2 mb-2">
                            <input type="text" name="produk_unggulan[]" placeholder="Nama produk unggulan"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <button type="button" onclick="addProduk()" class="px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">Klik tombol + untuk menambah produk unggulan</p>
                </div>

                <!-- Foto Usaha -->
                <div class="lg:col-span-2">
                    <label for="foto_usaha" class="block text-sm font-medium text-gray-700 mb-2">Foto Usaha</label>

                    @if($umkm->foto_usaha && is_array($umkm->foto_usaha) && count($umkm->foto_usaha) > 0)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($umkm->foto_usaha as $foto)
                            <div class="relative">
                                <img src="{{ Storage::url($foto) }}" alt="Foto usaha"
                                     class="w-full h-24 object-cover rounded-lg border border-gray-300">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <input type="file" name="foto_usaha[]" id="foto_usaha" accept="image/*" multiple
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('foto_usaha') border-red-500 @enderror">
                    <p class="mt-2 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB per file. Kosongkan jika tidak ingin mengubah foto.</p>
                    @error('foto_usaha')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="lg:col-span-2">
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_unggulan" value="1" {{ old('is_unggulan', $umkm->is_unggulan) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Usaha Unggulan</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $umkm->is_verified) ? 'checked' : '' }}
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Terverifikasi</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Update UMKM
                </button>
                <a href="{{ route('umkm.show', $umkm) }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('umkm.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors shadow-md">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@noncescript
const masterRwOptions = @json($masterRwOptions);
const currentRtId = "{{ $umkm->rt_id }}";

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
            if (initial && String(rt.id) === String(currentRtId)) {
                opt.selected = true;
            }
            rtSelect.appendChild(opt);
        });
    }
    if (!initial) syncDusunFromRt();
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

// Initial populate
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('rw_id').value) {
        populateRtByRw(true);
    }
});

// Add produk unggulan
function addProduk() {
    const container = document.getElementById('produk-container');
    const div = document.createElement('div');
    div.className = 'flex space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="produk_unggulan[]" placeholder="Nama produk unggulan"
               class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
        <button type="button" onclick="removeProduk(this)" class="px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove produk unggulan
function removeProduk(button) {
    button.parentElement.remove();
}

// SweetAlert untuk notifikasi sukses
@if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonText: 'OK'
    });
@endif

// SweetAlert untuk notifikasi error
@if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
@endif
@endnoncescript
@endsection

