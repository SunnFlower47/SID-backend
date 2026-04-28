@extends('layouts.app')

@section('title', 'Master Wilayah')
@section('subtitle', 'Kelola Dusun, RW, dan RT')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <h1 class="text-2xl font-bold">Master Wilayah</h1>
        <p class="text-emerald-100 mt-1">Mapping wilayah untuk validasi RT/RW/Dusun yang fleksibel.</p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-emerald-950">
            <div class="bg-white/90 rounded-xl p-3">
                <div class="text-xs uppercase tracking-wide text-emerald-700">Dusun</div>
                <div class="text-2xl font-bold">{{ $summary['dusun'] ?? 0 }}</div>
            </div>
            <div class="bg-white/90 rounded-xl p-3">
                <div class="text-xs uppercase tracking-wide text-emerald-700">RW</div>
                <div class="text-2xl font-bold">{{ $summary['rw'] ?? 0 }}</div>
            </div>
            <div class="bg-white/90 rounded-xl p-3">
                <div class="text-xs uppercase tracking-wide text-emerald-700">RT</div>
                <div class="text-2xl font-bold">{{ $summary['rt'] ?? 0 }}</div>
            </div>
            <div class="bg-white/90 rounded-xl p-3">
                <div class="text-xs uppercase tracking-wide text-emerald-700">Penduduk Terpetakan</div>
                <div class="text-2xl font-bold">{{ $summary['penduduk_terpetakan'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">{{ session('error') }}</div>
    @endif

    @php($pv = session('preview_impact'))

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Tambah Dusun</h3>
            <form method="POST" action="{{ route('settings.wilayah.dusun.store') }}" class="space-y-3">
                @csrf
                <input name="nama" placeholder="Nama dusun" class="w-full border rounded-lg px-3 py-2" required>
                <input name="kode" placeholder="Kode (opsional)" class="w-full border rounded-lg px-3 py-2">
                <button class="w-full bg-emerald-600 text-white rounded-lg py-2">Simpan Dusun</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Tambah RW</h3>
            <form method="POST" action="{{ route('settings.wilayah.rw.store') }}" class="space-y-3">
                @csrf
                <input name="kode" placeholder="Kode RW (contoh: 001)" class="w-full border rounded-lg px-3 py-2" required>
                <input name="nama" placeholder="Nama RW (opsional)" class="w-full border rounded-lg px-3 py-2">
                <button class="w-full bg-blue-600 text-white rounded-lg py-2">Simpan RW</button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-4 sm:p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Tambah RT</h3>
            <form method="POST" action="{{ route('settings.wilayah.rt.store') }}" class="space-y-3">
                @csrf
                <input name="kode" placeholder="Kode RT (contoh: 001)" class="w-full border rounded-lg px-3 py-2" required>
                <select name="rw_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Pilih RW</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}">{{ $rw->kode }} - {{ $rw->nama }}</option>
                    @endforeach
                </select>
                <select name="dusun_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Pilih Dusun (opsional)</option>
                    @foreach($dusuns as $dusun)
                        <option value="{{ $dusun->id }}">{{ $dusun->nama }}</option>
                    @endforeach
                </select>
                <button class="w-full bg-purple-600 text-white rounded-lg py-2">Simpan RT</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Daftar RT Mapping</h3>
            <span class="text-xs text-gray-500">Tip: Preview dulu sebelum update/hapus</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr class="text-gray-600 uppercase tracking-wider text-xs">
                        <th class="px-4 py-3 text-left font-semibold">RT</th>
                        <th class="px-4 py-3 text-left font-semibold">RW</th>
                        <th class="px-4 py-3 text-left font-semibold">Dusun</th>
                        <th class="px-4 py-3 text-left font-semibold">Penduduk</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold min-w-[360px]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rts as $rt)
                        <tr class="hover:bg-gray-50 transition-colors border-t align-top">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $rt->kode }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $rt->rw->kode ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $rt->dusun->nama ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 font-semibold">{{ $rt->penduduk_count ?? 0 }} orang</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($rt->needs_review)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800 font-medium">Needs Review</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-medium">OK</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <form method="POST" action="{{ route('settings.wilayah.rt.update', $rt) }}" class="space-y-3 bg-gray-50 border border-gray-200 rounded-xl p-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="kode" value="{{ $rt->kode }}">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <label class="text-xs text-gray-600">RW
                                            <select name="rw_id" class="mt-1 w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs bg-white">
                                                @foreach($rws as $rw)
                                                    <option value="{{ $rw->id }}" {{ $rt->rw_id == $rw->id ? 'selected' : '' }}>{{ $rw->kode }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="text-xs text-gray-600">Dusun
                                            <select name="dusun_id" class="mt-1 w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs bg-white">
                                                <option value="">-</option>
                                                @foreach($dusuns as $dusun)
                                                    <option value="{{ $dusun->id }}" {{ $rt->dusun_id == $dusun->id ? 'selected' : '' }}>{{ $dusun->nama }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 text-xs">
                                        <label class="inline-flex items-center gap-1"><input type="checkbox" name="needs_review" value="1" {{ $rt->needs_review ? 'checked' : '' }}> Needs Review</label>
                                        <label class="inline-flex items-center gap-1"><input type="checkbox" name="is_active" value="1" {{ $rt->is_active ? 'checked' : '' }}> Aktif</label>
                                    </div>

                                    <div class="flex flex-wrap gap-2 pt-1 border-t border-gray-200">
                                        <a href="{{ route('settings.wilayah.rt.penduduk', $rt) }}" class="px-2.5 py-1.5 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white rounded-lg text-xs shadow-sm">Detail Penduduk</a>
                                        <button type="submit" formaction="{{ route('settings.wilayah.rt.preview-impact', $rt) }}" formmethod="POST" class="px-2.5 py-1.5 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-lg text-xs shadow-sm">Preview Impact</button>
                                        <span class="px-2.5 py-1.5 bg-gray-200 text-gray-600 rounded-lg text-xs shadow-sm">Update via Preview+Apply</span>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('settings.wilayah.rt.destroy', $rt) }}" class="inline-flex mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="px-2.5 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-xs shadow-sm {{ ($rt->penduduk_count ?? 0) > 0 ? 'opacity-50 cursor-not-allowed' : 'hover:from-red-700 hover:to-red-800' }}"
                                        {{ ($rt->penduduk_count ?? 0) > 0 ? 'disabled' : '' }}
                                        title="{{ ($rt->penduduk_count ?? 0) > 0 ? 'Tidak bisa dihapus karena masih ada penduduk' : 'Hapus RT ini' }}"
                                        onclick="if(this.disabled){return false;} event.preventDefault(); const f=this.closest('form'); if(window.Swal){Swal.fire({title:'Hapus RT?',text:'Data RT yang kosong akan dihapus permanen.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Hapus',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then((r)=>{if(r.isConfirmed){f.submit();}});} else if(confirm('Yakin hapus RT ini?')){f.submit();} return false;"
                                    >Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data RT mapping.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="previewImpactModal" class="fixed inset-0 z-[100] {{ $pv ? '' : 'hidden' }}">
    <a href="{{ route('settings.wilayah.index') }}" class="absolute inset-0 bg-black/50" id="previewImpactBackdrop" aria-label="Tutup preview modal"></a>
    <div class="absolute inset-0 overflow-y-auto p-4 md:p-8">
        <div class="mx-auto w-full max-w-5xl bg-white rounded-2xl shadow-2xl border border-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Preview Impact RT</h3>
                <a href="{{ route('settings.wilayah.index') }}" id="closePreviewModal" class="text-gray-500 hover:text-gray-700 text-2xl leading-none" aria-label="Tutup">&times;</a>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="text-xs uppercase tracking-wide text-amber-700">Sebelum</div>
                        <div class="font-bold text-lg">RT {{ $pv['before']['rt'] ?? '-' }} / RW {{ $pv['before']['rw'] ?? '-' }}</div>
                        <div class="text-sm text-amber-900">Dusun: {{ $pv['before']['dusun'] ?? '-' }}</div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <div class="text-xs uppercase tracking-wide text-emerald-700">Sesudah</div>
                        <div class="font-bold text-lg">RT {{ $pv['after']['rt'] ?? '-' }} / RW {{ $pv['after']['rw'] ?? '-' }}</div>
                        <div class="text-sm text-emerald-900">Dusun: {{ $pv['after']['dusun'] ?? '-' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                        <div class="text-xs uppercase tracking-wide text-sky-700">Penduduk di RT Saat Ini</div>
                        <div class="text-3xl font-bold text-sky-900">{{ $pv['current_count'] ?? 0 }} orang</div>
                    </div>
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                        <div class="text-xs uppercase tracking-wide text-rose-700">Terdampak Perubahan</div>
                        <div class="text-3xl font-bold text-rose-900">{{ $pv['affected_count'] ?? 0 }} orang</div>
                    </div>
                </div>

                <div>
                    <div class="font-semibold text-gray-900 mb-2">Sample Penduduk Terdampak</div>
                    <div class="border rounded-xl max-h-80 overflow-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-left">NIK</th>
                                    <th class="px-3 py-2 text-left">NKK</th>
                                    <th class="px-3 py-2 text-left">RT/RW</th>
                                    <th class="px-3 py-2 text-left">Dusun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($pv['sample'] ?? []) as $s)
                                    <tr class="border-t">
                                        <td class="px-3 py-2">{{ $s['nama'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-xs font-mono">{{ $s['nik'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-xs font-mono">{{ $s['nkk'] ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            @if(($s['rt_label'] ?? '-') !== '-')
                                                RT {{ $s['rt_label'] }} / RW {{ $s['rw_label'] }}
                                            @else
                                                <span class="text-red-500 font-bold">BELUM TERPETAKAN</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">{{ $s['dusun_label'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500">Tidak ada sample data terdampak.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t flex flex-wrap justify-end gap-2">
                @if($pv)
                    <form method="POST" action="{{ route('settings.wilayah.rt.apply-update', $pv['id']) }}">
                        @csrf
                        <input type="hidden" name="preview_token" value="{{ $pv['preview_token'] ?? '' }}">
                        <input type="hidden" name="kode" value="{{ $pv['apply_payload']['kode'] ?? '' }}">
                        <input type="hidden" name="rw_id" value="{{ $pv['apply_payload']['rw_id'] ?? '' }}">
                        <input type="hidden" name="dusun_id" value="{{ $pv['apply_payload']['dusun_id'] ?? '' }}">
                        <input type="hidden" name="nama" value="{{ $pv['apply_payload']['nama'] ?? '' }}">
                        <input type="hidden" name="is_active" value="{{ !empty($pv['apply_payload']['is_active']) ? 1 : 0 }}">
                        <input type="hidden" name="needs_review" value="{{ !empty($pv['apply_payload']['needs_review']) ? 1 : 0 }}">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg" onclick="event.preventDefault(); const f=this.closest('form'); if(!f){return false;} if(window.Swal){Swal.fire({title:'Lanjutkan Apply?',text:'Sistem akan auto-backup sebelum perubahan diterapkan.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Apply + Backup',cancelButtonText:'Batal'}).then((r)=>{if(r.isConfirmed){f.submit();}});} else if(confirm('Lanjutkan apply? Sistem akan auto-backup terlebih dahulu.')){f.submit();} return false;">Lanjutkan Apply + Backup</button>
                    </form>
                @endif
                <a href="{{ route('settings.wilayah.index') }}" id="closePreviewModalBottom" class="bg-gray-800 text-white px-4 py-2 rounded-lg inline-block">Tutup</a>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden mt-6">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Riwayat Perubahan Wilayah (Audit)</h3>
        <p class="text-sm text-gray-600">Log apply + backup + rollback untuk perubahan RT</p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-gray-600 uppercase tracking-wider text-xs">
                    <th class="px-4 py-3 text-left font-semibold">Waktu</th>
                    <th class="px-4 py-3 text-left font-semibold">Before</th>
                    <th class="px-4 py-3 text-left font-semibold">After</th>
                    <th class="px-4 py-3 text-left font-semibold">Affected</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($recentChangeLogs ?? collect()) as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ optional($log->applied_at ?? $log->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-700 text-xs">RT {{ data_get($log->before_payload, 'rt', '-') }} / RW {{ data_get($log->before_payload, 'rw', '-') }}<br>Dusun: {{ data_get($log->before_payload, 'dusun', '-') }}</td>
                        <td class="px-4 py-3 text-gray-700 text-xs">RT {{ data_get($log->after_payload, 'rt', '-') }} / RW {{ data_get($log->after_payload, 'rw', '-') }}<br>Dusun: {{ data_get($log->after_payload, 'dusun', '-') }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-semibold">{{ $log->affected_count }} org</span></td>
                        <td class="px-4 py-3">
                            @if($log->status === 'rolled_back')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 font-medium">Rolled Back</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-medium">Applied</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->status === 'applied')
                                <form method="POST" action="{{ route('settings.wilayah.change-log.rollback', $log->id) }}" class="rollback-form inline-flex">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-xs shadow-sm hover:from-red-700 hover:to-red-800" onclick="event.preventDefault(); const f=this.closest('form'); if(!f){return false;} if(window.Swal){Swal.fire({title:'Rollback perubahan ini?',text:'Data akan dikembalikan ke snapshot sebelum apply.',icon:'warning',showCancelButton:true,confirmButtonText:'Ya, Rollback',cancelButtonText:'Batal',confirmButtonColor:'#dc2626'}).then((r)=>{if(r.isConfirmed){f.submit();}});} else if(confirm('Rollback perubahan ini?')){f.submit();} return false;">Rollback</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat perubahan wilayah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection



