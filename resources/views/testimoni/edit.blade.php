@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 via-orange-700 to-orange-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-yellow-300 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Edit Testimoni</h1>
                <p class="text-orange-100 mt-1">Ubah data testimoni warga desa</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <!-- Global Error Display -->
        @if($errors->any())
            <div class="m-6 bg-red-50 border border-red-200 rounded-lg p-4">
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

        <form action="{{ route('testimoni.update', $testimoni) }}" method="POST" class="p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-orange-500 mr-2"></i>
                        Nama Lengkap *
                    </label>
                    <input type="text"
                           id="nama"
                           name="nama"
                           value="{{ old('nama', $testimoni->nama) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-orange-500 mr-2"></i>
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email', $testimoni->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('email') border-red-500 @enderror"
                           placeholder="contoh@email.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div>
                    <label for="telepon" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone text-orange-500 mr-2"></i>
                        Nomor Telepon
                    </label>
                    <input type="text"
                           id="telepon"
                           name="telepon"
                           value="{{ old('telepon', $testimoni->telepon) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('telepon') border-red-500 @enderror"
                           placeholder="08xxxxxxxxxx">
                    @error('telepon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rating -->
                <div>
                    <label for="rating" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-star text-orange-500 mr-2"></i>
                        Rating (1-5)
                    </label>
                    <select id="rating"
                            name="rating"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('rating') border-red-500 @enderror">
                        <option value="">Pilih Rating</option>
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}" {{ old('rating', $testimoni->rating) == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                        @endfor
                    </select>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tags text-orange-500 mr-2"></i>
                        Kategori
                    </label>
                    <select id="kategori"
                            name="kategori"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('kategori') border-red-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="layanan" {{ old('kategori', $testimoni->kategori) == 'layanan' ? 'selected' : '' }}>Layanan</option>
                        <option value="pelayanan" {{ old('kategori', $testimoni->kategori) == 'pelayanan' ? 'selected' : '' }}>Pelayanan</option>
                        <option value="infrastruktur" {{ old('kategori', $testimoni->kategori) == 'infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                        <option value="program" {{ old('kategori', $testimoni->kategori) == 'program' ? 'selected' : '' }}>Program Desa</option>
                        <option value="umum" {{ old('kategori', $testimoni->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                    @error('kategori')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle text-orange-500 mr-2"></i>
                        Status
                    </label>
                    <select id="status"
                            name="status"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('status') border-red-500 @enderror"
                            required>
                        <option value="pending" {{ old('status', $testimoni->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ old('status', $testimoni->status) == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ old('status', $testimoni->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RT, RW, Dusun -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="rw_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                            RW Master <span class="text-red-500">*</span>
                        </label>
                        <select id="rw_id" name="rw_id" onchange="populateRtByRw()" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('rw_id') border-red-500 @enderror">
                            <option value="">Pilih RW</option>
                            @foreach($masterRwOptions as $rw)
                                <option value="{{ $rw['id'] }}" {{ old('rw_id', $testimoni->rw_id) == $rw['id'] ? 'selected' : '' }}>RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
                            @endforeach
                        </select>
                        @error('rw_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rt_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                            RT Master <span class="text-red-500">*</span>
                            @if(!$testimoni->rt_id)
                                <span class="text-red-600 text-xs font-bold animate-pulse">(DATA TIDAK VALID)</span>
                            @endif
                        </label>
                        <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @if(!$testimoni->rt_id) border-red-500 bg-red-50 @else border-gray-300 @endif @error('rt_id') border-red-500 @enderror">
                            <option value="">Pilih RT</option>
                        </select>
                        @error('rt_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dusun_display" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map text-orange-500 mr-2"></i>
                            Dusun
                        </label>
                        <input type="text" id="dusun_display" value="{{ $testimoni->dusun_label }}" disabled
                               class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-gray-500 text-base"
                               placeholder="Otomatis dari RT">
                        <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id', $testimoni->dusun_id) }}">
                    </div>
                </div>

                <!-- Anonymous -->
                <div class="lg:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="is_anonymous"
                               name="is_anonymous"
                               value="1"
                               {{ old('is_anonymous', $testimoni->is_anonymous) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                        <label for="is_anonymous" class="ml-2 text-sm font-medium text-gray-700">
                            Tampilkan sebagai Warga Anonim
                        </label>
                    </div>
                </div>
            </div>

            <!-- Testimoni -->
            <div class="mt-6">
                <label for="testimoni" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment text-orange-500 mr-2"></i>
                    Testimoni *
                </label>
                <textarea id="testimoni"
                          name="testimoni"
                          rows="6"
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-base @error('testimoni') border-red-500 @enderror"
                          placeholder="Tuliskan testimoni Anda tentang pelayanan desa..."
                          required>{{ old('testimoni', $testimoni->testimoni) }}</textarea>
                @error('testimoni')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end mt-8">
                <a href="{{ route('testimoni.index') }}"
                   class="flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 bg-white rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-xl hover:from-orange-700 hover:to-orange-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Update Testimoni
                </button>
            </div>
        </form>
    </div>
</div>

@noncescript
const masterRwOptions = @json($masterRwOptions);
const currentRtId = "{{ $testimoni->rt_id }}";

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
@endnoncescript
@endsection
