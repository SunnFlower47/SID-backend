@extends('layouts.app')

@section('title', 'Edit Kartu Keluarga')
@section('subtitle', 'Edit data Kartu Keluarga')

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
                    <h1 class="text-2xl sm:text-3xl font-bold">Edit Kartu Keluarga</h1>
                    <p class="text-green-100 text-sm sm:text-base">NKK: {{ $nkk }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kartu-keluarga.show', $nkk) }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-eye mr-2"></i>
                    Lihat Detail
                </a>
                <a href="{{ route('kartu-keluarga.index') }}" class="group flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-green-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Form Edit Kartu Keluarga</h3>
                <p class="text-sm text-gray-600">Ubah data Kartu Keluarga dengan benar</p>
            </div>
        </div>

        <form action="{{ route('kartu-keluarga.update', $nkk) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Data Kepala Keluarga -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-crown text-yellow-500 mr-2"></i>
                        Data Kepala Keluarga
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kepala Keluarga -->
                    <div>
                        <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga"
                               value="{{ old('nama_kepala_keluarga', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->nama ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_kepala_keluarga') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap" required>
                        @error('nama_kepala_keluarga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="alamat" id="alamat"
                               value="{{ old('alamat', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->alamat ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror"
                               placeholder="Masukkan alamat lengkap" required>
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
                                <option value="{{ $rw['id'] }}" {{ old('rw_id', $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first()->rw_id ?? '') == $rw['id'] ? 'selected' : '' }}>RW {{ $rw['kode'] }} - {{ $rw['nama'] }}</option>
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
                            @php
                                $kepala = $kartuKeluarga->where('kedudukan_keluarga', 'Kepala Keluarga')->first();
                            @endphp
                            @if($kepala && !$kepala->rt_id)
                                <span class="text-red-600 text-xs font-bold animate-pulse">(DATA TIDAK VALID)</span>
                            @endif
                        </label>
                        <select id="rt_id" name="rt_id" onchange="syncDusunFromRt()" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @if($kepala && !$kepala->rt_id) border-red-500 bg-red-50 @else border-gray-300 @endif @error('rt_id') border-red-500 @enderror">
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
                               placeholder="Otomatis dari RT" value="{{ $kepala->dusun_label ?? '' }}">
                        <input type="hidden" name="dusun_id" id="dusun_id" value="{{ old('dusun_id', $kepala->dusun_id ?? '') }}">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('kartu-keluarga.show', $nkk) }}"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@noncescript
const masterRwOptions = @json($masterRwOptions);
const currentRtId = "{{ $kepala->rt_id ?? '' }}";

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
@endpush
@endsection

