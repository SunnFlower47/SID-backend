@extends('layouts.app')

@section('title', 'Master Wilayah')
@section('subtitle', 'Pengaturan Dusun, RW, dan RT')

@section('content')
<div class="space-y-6">
    <!-- Header Wilayah -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-8 relative overflow-hidden mb-6">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                    <i class="fas fa-map text-2xl text-yellow-300"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight uppercase italic leading-none">Master Wilayah</h1>
                    <p class="text-emerald-100 font-bold text-xs uppercase tracking-widest mt-1 opacity-80">Manajemen Dusun, RW, dan RT</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/90 rounded-xl p-3">
                    <div class="text-xs uppercase tracking-wide text-emerald-700">Total RT</div>
                    <div class="text-2xl font-bold">{{ $summary['rt'] ?? 0 }}</div>
                </div>
                <div class="bg-white/90 rounded-xl p-3">
                    <div class="text-xs uppercase tracking-wide text-emerald-700">Tetap Terpetakan</div>
                    <div class="text-2xl font-bold">{{ $summary['penduduk_terpetakan'] ?? 0 }}</div>
                </div>
                <div class="bg-white/90 rounded-xl p-3">
                    <div class="text-xs uppercase tracking-wide text-emerald-700">Domisili Terpetakan</div>
                    <div class="text-2xl font-bold">{{ $summary['domisili_terpetakan'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-green-500 transition-all cursor-pointer" onclick="openModal('dusun')">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-green-600 transition-all">
                <i class="fas fa-plus text-green-600 group-hover:text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900">Tambah Dusun</h3>
            <p class="text-sm text-gray-500">Buat wilayah dusun baru</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-blue-500 transition-all cursor-pointer" onclick="openModal('rw')">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-600 transition-all">
                <i class="fas fa-plus text-blue-600 group-hover:text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900">Tambah RW</h3>
            <p class="text-sm text-gray-500">Buat wilayah RW baru</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-purple-500 transition-all cursor-pointer" onclick="openModal('rt')">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-600 transition-all">
                <i class="fas fa-plus text-purple-600 group-hover:text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900">Tambah RT</h3>
            <p class="text-sm text-gray-500">Buat wilayah RT baru</p>
        </div>
    </div>

    <!-- List Wilayah -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Pemetaan Wilayah (RT)</h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">{{ $summary['rt'] }} RT Terdaftar</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">RT</th>
                        <th class="px-4 py-3 text-left font-semibold">RW</th>
                        <th class="px-4 py-3 text-left font-semibold">Dusun</th>
                        <th class="px-4 py-3 text-left font-semibold">Penduduk Tetap</th>
                        <th class="px-4 py-3 text-left font-semibold">Domisili</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold min-w-[360px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mapping['rts'] as $rt)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-bold text-gray-900">RT {{ $rt->kode }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $rt->rw->kode ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $rt->dusun->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 font-semibold">{{ $rt->penduduk_count ?? 0 }} org</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-semibold">{{ $rt->domisili_count ?? 0 }} org</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($rt->needs_review)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Perlu Review
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">
                                        <i class="fas fa-check-circle mr-1"></i> Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button onclick="editWilayah('rt', {{ json_encode($rt) }})" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <a href="{{ route('settings.wilayah.rt.penduduk', $rt->id) }}" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        <i class="fas fa-users mr-1"></i> Detail Warga
                                    </a>
                                    @if($rt->penduduk_count == 0)
                                    <button onclick="confirmDelete('rt', {{ $rt->id }}, '{{ $rt->kode }}')" class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Container -->
<div id="wilayahModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-in zoom-in duration-300">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 uppercase italic">Tambah Dusun</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="wilayahForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formType" name="type">
            <input type="hidden" id="modelId" name="id">

            <div id="parentFields" class="hidden space-y-4">
                <div id="dusunSelect" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-1">Pilih Dusun</label>
                    <select name="dusun_id" class="w-full rounded-xl border-gray-200 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih Dusun --</option>
                        @foreach($mapping['dusuns'] as $dusun)
                            <option value="{{ $dusun->id }}">{{ $dusun->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="rwSelect" class="hidden">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-1">Pilih RW</label>
                    <select name="rw_id" class="w-full rounded-xl border-gray-200 focus:ring-green-500 focus:border-green-500">
                        <option value="">-- Pilih RW --</option>
                        @foreach($mapping['rws'] as $rw)
                            <option value="{{ $rw->id }}">RW {{ $rw->kode }} ({{ $rw->dusun->nama ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label id="kodeLabel" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-1">Nama/Kode</label>
                <input type="text" name="kode" required class="w-full rounded-xl border-gray-200 focus:ring-green-500 focus:border-green-500" placeholder="Contoh: Dusun I atau 001">
            </div>

            <div id="impactSection" class="hidden p-4 bg-yellow-50 border border-yellow-100 rounded-xl">
                <div class="flex items-center text-yellow-800 font-bold mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Analisis Dampak Perubahan
                </div>
                <div id="impactMessage" class="text-xs text-yellow-700 space-y-1"></div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeModal()" class="px-6 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">
                    BATAL
                </button>
                <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 shadow-lg shadow-green-200 transition-all">
                    SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let isEditing = false;

    function openModal(type) {
        isEditing = false;
        const modal = document.getElementById('wilayahModal');
        const form = document.getElementById('wilayahForm');
        const title = document.getElementById('modalTitle');
        const parentFields = document.getElementById('parentFields');
        const dusunSelect = document.getElementById('dusunSelect');
        const rwSelect = document.getElementById('rwSelect');
        const impactSection = document.getElementById('impactSection');
        
        form.reset();
        document.getElementById('formType').value = type;
        document.getElementById('modelId').value = '';
        impactSection.classList.add('hidden');

        if (type === 'dusun') {
            title.innerText = 'Tambah Dusun Baru';
            parentFields.classList.add('hidden');
            document.getElementById('kodeLabel').innerText = 'Nama Dusun';
        } else if (type === 'rw') {
            title.innerText = 'Tambah RW Baru';
            parentFields.classList.remove('hidden');
            dusunSelect.classList.remove('hidden');
            rwSelect.classList.add('hidden');
            document.getElementById('kodeLabel').innerText = 'Kode RW (Contoh: 001)';
        } else {
            title.innerText = 'Tambah RT Baru';
            parentFields.classList.remove('hidden');
            dusunSelect.classList.add('hidden');
            rwSelect.classList.remove('hidden');
            document.getElementById('kodeLabel').innerText = 'Kode RT (Contoh: 001)';
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('wilayahModal').classList.add('hidden');
    }

    function editWilayah(type, data) {
        openModal(type);
        isEditing = true;
        document.getElementById('modalTitle').innerText = 'Edit ' + type.toUpperCase() + ' ' + data.kode;
        document.getElementById('modelId').value = data.id;
        document.getElementsByName('kode')[0].value = data.kode;
        
        if (type === 'rw') {
            document.getElementsByName('dusun_id')[0].value = data.dusun_id;
        } else if (type === 'rt') {
            document.getElementsByName('rw_id')[0].value = data.rw_id;
        }

        // Trigger impact analysis
        checkImpact(type, data.id, data.kode);
    }

    async function checkImpact(type, id, newValue) {
        if (!isEditing) return;
        
        try {
            const response = await fetch(`/admin/settings/wilayah/${type}/${id}/preview-impact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ kode: newValue })
            });
            const data = await response.json();
            
            if (data.impacted_residents > 0) {
                const impactSection = document.getElementById('impactSection');
                const impactMessage = document.getElementById('impactMessage');
                impactSection.classList.remove('hidden');
                impactMessage.innerHTML = `
                    <p>• Mengubah ${type.toUpperCase()} ini akan memengaruhi <b>${data.impacted_residents}</b> penduduk.</p>
                    <p>• Sistem akan memperbarui alamat otomatis pada Kartu Keluarga terkait.</p>
                    <p>• Perubahan akan dicatat dalam Audit Log.</p>
                `;
            }
        } catch (e) {
            console.error('Impact check failed');
        }
    }

    document.getElementById('wilayahForm').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const type = formData.get('type');
        const id = formData.get('id');
        
        const url = id ? `/admin/settings/wilayah/${type}/${id}` : `/admin/settings/wilayah/${type}`;
        const method = id ? 'PUT' : 'POST';

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (response.ok) {
                window.location.reload();
            } else {
                alert(result.message || 'Gagal menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        }
    };

    function confirmDelete(type, id, label) {
        if (confirm(`Apakah Anda yakin ingin menghapus ${type.toUpperCase()} ${label}?`)) {
            fetch(`/admin/settings/wilayah/${type}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (res.ok) window.location.reload();
                else alert('Gagal menghapus data');
            });
        }
    }
</script>
@endpush

@endsection
