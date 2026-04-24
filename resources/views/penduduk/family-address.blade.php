@extends('layouts.app')

@section('title', 'Update Alamat Keluarga')
@section('subtitle', 'Update alamat untuk seluruh anggota keluarga sekaligus')

@section('content')
<div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-2xl shadow-2xl border-0 p-6 sm:p-8 mb-8">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="flex-1 text-center lg:text-left mb-6 lg:mb-0">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 flex items-center justify-center lg:justify-start">
                        <i class="fas fa-home mr-3 text-yellow-300"></i>
                        Update Alamat Keluarga
                    </h1>
                    <p class="text-green-100 text-lg mb-2">Update alamat untuk seluruh anggota keluarga sekaligus</p>
                    <p class="text-green-200 text-sm sm:text-base">
                        @if($kepalaKeluarga)
                            <i class="fas fa-users mr-2"></i>
                            Keluarga: {{ $kepalaKeluarga->nama }} ({{ $familyMembers->count() }} anggota)
                        @else
                            <i class="fas fa-id-card mr-2"></i>
                            No KK: {{ $nkk }} ({{ $familyMembers->count() }} anggota)
                        @endif
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-home text-white text-2xl sm:text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Members Info -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-users text-green-500 mr-3"></i>
                    Anggota Keluarga yang akan diupdate
                </h3>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($familyMembers as $member)
                    <div class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 border-0 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-900 truncate">{{ $member->nama }}</div>
                                <div class="text-sm text-blue-600 font-medium">{{ $member->kedudukan_keluarga }}</div>
                                <div class="text-xs text-gray-500 font-mono">NIK: {{ $member->nik }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit text-green-500 mr-3"></i>
                    Form Update Alamat
                </h3>
                <div class="hidden sm:block w-8 h-1 bg-gradient-to-r from-green-500 to-green-600 rounded-full"></div>
            </div>

            <form action="{{ route('penduduk.family.address.update', $nkk) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Current Address Info -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-map-marker-alt text-white text-sm"></i>
                        </div>
                        <h4 class="text-lg font-bold text-yellow-900">Alamat Saat Ini</h4>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <p class="text-yellow-800 font-medium mb-2">{{ $currentAddress->alamat }}</p>
                        <p class="text-yellow-700 text-sm">RT {{ $currentAddress->rt }} / RW {{ $currentAddress->rw }} - {{ $currentAddress->dusun }}</p>
                    </div>
                </div>

                <!-- New Address Form -->
                <div class="space-y-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-home text-white text-sm"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Alamat Baru</h3>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-3">
                            <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                            Alamat Lengkap *
                        </label>
                        <textarea id="alamat" name="alamat" rows="4" required
                                  class="w-full px-4 py-3 border-0 rounded-2xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base bg-white shadow-lg @error('alamat') border-red-300 @enderror"
                                  placeholder="Masukkan alamat lengkap baru">{{ old('alamat', $currentAddress->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6">
                            <label for="rw_id" class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-building text-purple-500 mr-2"></i>
                                RW Master *
                            </label>
                            <select id="rw_id" name="rw_id" required
                                    class="w-full px-4 py-3 border-0 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base bg-white shadow-lg @error('rw_id') border-red-300 @enderror">
                                <option value="">Pilih RW</option>
                                @foreach(($rws ?? collect()) as $rw)
                                    <option value="{{ $rw->id }}" {{ (string)old('rw_id') === (string)$rw->id ? 'selected' : '' }}>RW {{ $rw->kode }} - {{ $rw->nama }}</option>
                                @endforeach
                            </select>
                            @error('rw_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6">
                            <label for="rt_id" class="block text-sm font-medium text-gray-700 mb-3">
                                <i class="fas fa-home text-blue-500 mr-2"></i>
                                RT Master *
                            </label>
                            <select id="rt_id" name="rt_id" required
                                    class="w-full px-4 py-3 border-0 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white shadow-lg @error('rt_id') border-red-300 @enderror">
                                <option value="">Pilih RT</option>
                            </select>
                            @error('rt_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW (kode)</label>
                            <input id="rw" name="rw" type="text" readonly value="{{ old('rw', $currentAddress->rw) }}" class="w-full px-4 py-3 rounded-2xl bg-gray-100 border-0">
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT (kode)</label>
                            <input id="rt" name="rt" type="text" readonly value="{{ old('rt', $currentAddress->rt) }}" class="w-full px-4 py-3 rounded-2xl bg-gray-100 border-0">
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">Dusun</label>
                            <input id="dusun" name="dusun" type="text" readonly value="{{ old('dusun', $currentAddress->dusun) }}" class="w-full px-4 py-3 rounded-2xl bg-gray-100 border-0">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end pt-8 border-t border-gray-200">
                    <a href="{{ route('penduduk.index') }}"
                       class="group flex items-center justify-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-arrow-left text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-lg">Batal</span>
                    </a>
                    <button type="submit"
                            class="group flex items-center justify-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-save text-white text-sm"></i>
                        </div>
                        <span class="font-bold text-lg">Update Alamat {{ $familyMembers->count() }} Anggota</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

<script nonce="{{ $csp_nonce }}">
const masterRwOptions = @json($masterRwOptions ?? []);

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

function syncWilayahCode() {
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

document.addEventListener('DOMContentLoaded', function () {
    const rwSelect = document.getElementById('rw_id');
    const rtSelect = document.getElementById('rt_id');
    if (!rwSelect || !rtSelect) return;

    const oldRwId = @json(old('rw_id'));
    const oldRtId = @json(old('rt_id'));

    if (oldRwId) {
        rwSelect.value = String(oldRwId);
    } else {
        const currentRwCode = String(@json($currentAddress->rw ?? '')).padStart(3, '0');
        const matchedRw = masterRwOptions.find(r => String(r.kode).padStart(3, '0') === currentRwCode);
        if (matchedRw) rwSelect.value = String(matchedRw.id);
    }

    populateRtByRw();

    if (oldRtId) {
        rtSelect.value = String(oldRtId);
    } else {
        const rwObj = masterRwOptions.find(r => String(r.id) === String(rwSelect.value));
        const currentRtCode = String(@json($currentAddress->rt ?? '')).padStart(3, '0');
        const matchedRt = (rwObj?.rts || []).find(rt => String(rt.kode).padStart(3, '0') === currentRtCode);
        if (matchedRt) rtSelect.value = String(matchedRt.id);
    }

    syncWilayahCode();

    rwSelect.addEventListener('change', function () {
        populateRtByRw();
        syncWilayahCode();
    });
    rtSelect.addEventListener('change', syncWilayahCode);
});
</script>
@endsection
