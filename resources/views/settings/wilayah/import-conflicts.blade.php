@extends('layouts.app')

@section('title', 'Resolusi Issue Import')
@section('subtitle', 'Perbaiki data yang bermasalah saat proses import')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Resolusi Issue Import</h1>
                <p class="text-slate-400 mt-2 text-lg">Kelola konflik wilayah, duplikasi NIK, dan perbaikan alamat dari antrean import.</p>
            </div>
            <div class="flex gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center min-w-[100px]">
                    <div class="text-xs uppercase tracking-wider text-slate-400 font-bold">Pending</div>
                    <div class="text-3xl font-black mt-1">{{ $conflicts->where('status', 'pending')->count() }}</div>
                </div>
                <div class="bg-emerald-500/20 backdrop-blur-md rounded-2xl p-4 border border-emerald-500/30 text-center min-w-[100px]">
                    <div class="text-xs uppercase tracking-wider text-emerald-400 font-bold">Resolved</div>
                    <div class="text-3xl font-black mt-1 text-emerald-400">{{ $conflicts->where('status', 'resolved')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Batch ID</label>
                <input type="text" name="batch_id" value="{{ request('batch_id') }}" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-500 transition-all" placeholder="Cari Batch...">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Status</label>
                <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-500 transition-all">
                    <option value="all">Semua Status</option>
                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ request('status')==='resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tipe Issue</label>
                <select name="issue_type" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-slate-500 transition-all">
                    <option value="all">Semua Tipe</option>
                    <option value="wilayah_conflict" {{ request('issue_type')==='wilayah_conflict' ? 'selected' : '' }}>Konflik Wilayah</option>
                    <option value="nik_conflict" {{ request('issue_type')==='nik_conflict' ? 'selected' : '' }}>Konflik NIK</option>
                    <option value="required_field_missing" {{ request('issue_type')==='required_field_missing' ? 'selected' : '' }}>Data Tidak Lengkap</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 rounded-xl transition-all shadow-lg shadow-slate-200">
                    Filter
                </button>
                <a href="{{ route('settings.wilayah.import-conflicts.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all font-bold">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Issue Cards -->
    <div class="space-y-4">
        @forelse($conflicts as $c)
            @php
                $isPending = $c->status === 'pending';
                $isResolved = $c->status === 'resolved';
                $isSuccess = ($c->reprocess_status ?? '') === 'success';
                $isFailed = ($c->reprocess_status ?? '') === 'failed';
                $isSkipped = ($c->reprocess_status ?? '') === 'skipped';
                
                $typeLabel = match($c->issue_type) {
                    'wilayah_conflict' => 'Konflik Wilayah',
                    'nik_conflict' => 'Konflik NIK',
                    'required_field_missing' => 'Data Kurang',
                    default => 'Issue Umum'
                };
            @endphp
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow group">
                <div class="flex flex-col lg:flex-row">
                    <!-- Left Section: Info -->
                    <div class="lg:w-1/3 p-6 border-b lg:border-b-0 lg:border-r border-slate-100 bg-slate-50/30">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-200 text-slate-700">
                                Row #{{ $c->row_number }}
                            </span>
                            @if($isPending)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-100 text-amber-700 animate-pulse">
                                    Pending
                                </span>
                            @elseif($isSuccess || $isSkipped)
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700">
                                    Selesai
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-100 text-rose-700">
                                    Gagal
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $c->nama ?: '(Tanpa Nama)' }}</h3>
                        <div class="space-y-1 text-sm text-slate-500 font-mono">
                            <div>NIK: {{ $c->nik ?: '-' }}</div>
                            <div>NKK: {{ $c->nkk ?: '-' }}</div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-white rounded-2xl border border-slate-200 text-xs">
                            <div class="font-bold text-slate-400 uppercase mb-1">Lokasi di Excel:</div>
                            <div class="text-slate-700 font-semibold">
                                RW {{ $c->rw_raw ?: '?' }} / RT {{ $c->rt_raw ?: '?' }}<br>
                                Dusun: {{ $c->dusun_raw ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section: Problem & Resolution -->
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span class="text-xs font-black uppercase tracking-tighter text-rose-600">{{ $typeLabel }}</span>
                            </div>
                            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl text-rose-800 text-sm mb-4 italic">
                                "{{ $c->reason }}"
                            </div>

                            @if($isResolved)
                                <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="text-xs font-bold text-emerald-700 uppercase">Keputusan Resolusi:</div>
                                        @if(!$isSuccess)
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.reset', $c) }}">
                                                @csrf
                                                <button class="text-[10px] font-bold text-slate-400 hover:text-rose-500 uppercase tracking-tighter transition-colors">
                                                    [ Batal / Undo ]
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="text-sm font-semibold text-emerald-900">
                                        {{ str_replace('_', ' ', strtoupper($c->resolution_action)) }}
                                    </div>
                                    @if($c->reprocess_message)
                                        <div class="mt-2 pt-2 border-t border-emerald-100 text-xs text-emerald-600 italic">
                                            Laporan: {{ $c->reprocess_message }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if($isPending || ($isResolved && !$isSuccess && !$isSkipped))
                                <!-- Trigger Modal Perbaikan -->
                                <button type="button" 
                                        class="btn-resolve-issue px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-lg shadow-slate-200 transition-all flex items-center gap-2"
                                        data-id="{{ $c->id }}"
                                        data-type="{{ $c->issue_type }}"
                                        data-nama="{{ $c->nama }}"
                                        data-nik="{{ $c->nik }}"
                                        data-nkk="{{ $c->nkk }}"
                                        data-alamat="{{ ($c->meta['incoming_alamat'] ?? null) ?: ($c->payload_raw['alamat'] ?? ($c->payload_raw['domisili'] ?? '')) }}"
                                        data-jk="{{ ($c->meta['incoming_jk'] ?? null) ?: ($c->payload_raw['jenis_kelamin'] ?? ($c->payload_raw['jk'] ?? '')) }}"
                                        data-rt="{{ $c->rt_raw }}"
                                        data-rw="{{ $c->rw_raw }}"
                                        data-dusun="{{ $c->dusun_raw }}"
                                        data-reason="{{ $c->reason }}"
                                        data-existing="{{ $c->existing_resident ? json_encode([
                                            'nama' => $c->existing_resident->nama,
                                            'nik' => $c->existing_resident->nik,
                                            'nkk' => $c->existing_resident->nkk,
                                            'alamat' => $c->existing_resident->alamat,
                                            'jk' => $c->existing_resident->jenis_kelamin,
                                            'rt' => $c->existing_resident->rt_label,
                                            'rw' => $c->existing_resident->rw_label,
                                            'dusun' => $c->existing_resident->dusun_label,
                                            'status' => $c->existing_resident->deleted_at ? 'Terhapus/Mutasi' : 'Aktif'
                                        ]) : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Ambil Tindakan / Perbaiki
                                </button>
                                
                                @if($isResolved && ($isFailed || ($c->reprocess_status === 'pending')))
                                    <form method="POST" action="{{ route('settings.wilayah.import-conflicts.reprocess', $c) }}">
                                        @csrf
                                        <button class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-100 transition-all flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Jalankan Reprocess
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="text-xs font-bold text-slate-400 italic">Issue ini sudah final.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border-2 border-dashed border-slate-200">
                <div class="text-slate-300 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Antrean Bersih!</h3>
                <p class="text-slate-500">Tidak ada issue import yang perlu diperbaiki saat ini.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $conflicts->links() }}
    </div>
</div>

<!-- RESOLUTION MODAL -->
<div id="resolveModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modalContent">
            <form id="resolveForm" method="POST" action="">
                @csrf
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900" id="modalTitle">Perbaiki Data</h2>
                        <p class="text-sm text-slate-500 mt-1">Lakukan penyesuaian data sebelum di-import ulang.</p>
                    </div>
                    <button type="button" onclick="closeResolveModal()" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    
                    <!-- Side-by-Side Comparison (Only for NIK Conflict) -->
                    <div id="comparison_section" class="hidden space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest">Perbandingan Data</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Existing Data -->
                            <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200">
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    Data di Database
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase">Nama</div>
                                        <div id="compare_old_nama" class="text-sm font-bold text-slate-700"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase">NIK</div>
                                            <div id="compare_old_nik" class="text-xs font-mono text-slate-600"></div>
                                        </div>
                                        <div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase">NKK</div>
                                            <div id="compare_old_nkk" class="text-xs font-mono text-slate-600"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase">J. Kelamin</div>
                                            <div id="compare_old_jk" class="text-xs text-slate-700"></div>
                                        </div>
                                        <div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase">Status</div>
                                            <div id="compare_old_status" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200 text-slate-700 inline-block"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase">Alamat</div>
                                        <div id="compare_old_alamat" class="text-xs text-slate-600 italic leading-tight"></div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase">Wilayah</div>
                                        <div id="compare_old_wilayah" class="text-xs font-bold text-slate-700"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- New Data (Excel) -->
                            <div class="bg-blue-50/50 rounded-3xl p-5 border border-blue-100">
                                <div class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Data Baru (Excel)
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-[9px] font-bold text-blue-400 uppercase">Nama</div>
                                        <div id="compare_new_nama" class="text-sm font-bold text-blue-900"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <div class="text-[9px] font-bold text-blue-400 uppercase">NIK</div>
                                            <div id="compare_new_nik" class="text-xs font-mono text-blue-800"></div>
                                        </div>
                                        <div>
                                            <div class="text-[9px] font-bold text-blue-400 uppercase">NKK</div>
                                            <div id="compare_new_nkk" class="text-xs font-mono text-blue-800"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-blue-400 uppercase">J. Kelamin</div>
                                        <div id="compare_new_jk" class="text-xs text-blue-900"></div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-blue-400 uppercase">Alamat</div>
                                        <div id="compare_new_alamat" class="text-xs text-blue-800 italic leading-tight"></div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-blue-400 uppercase">Wilayah</div>
                                        <div id="compare_new_wilayah" class="text-xs font-bold text-blue-900"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-xl text-[11px] text-blue-700">
                            <strong>Tip:</strong> Bandingkan data di atas. Gunakan form di bawah jika ingin mengedit data Excel sebelum di-import/update.
                        </div>
                    </div>

                    <!-- Field Perbaikan Utama -->
                    <input type="hidden" name="rw_id" id="field_rw_id">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_new" id="field_nama" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">NIK (16 Digit)</label>
                            <input type="text" name="nik_new" id="field_nik" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-slate-500 transition-all">
                            <div id="nik_error" class="mt-1 text-[10px] font-bold hidden"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">NKK (16 Digit)</label>
                            <input type="text" name="nkk_new" id="field_nkk" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-slate-500 transition-all">
                            <div id="nkk_error" class="mt-1 text-[10px] font-bold hidden"></div>
                        </div>
                        
                        <!-- Issue Reason Highlight -->
                        <div class="md:col-span-2">
                            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 flex gap-3 items-start">
                                <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-rose-600 uppercase tracking-widest">Masalah Ditemukan:</div>
                                    <div id="modal_reason_text" class="text-sm font-bold text-rose-900 mt-0.5 italic"></div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Alamat Lengkap / Jalan / Dusun</label>
                            <textarea name="alamat_new" id="field_alamat" rows="2" class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-slate-500 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Field Wilayah -->
                    <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                        <h4 class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest">Penyesuaian Wilayah Master</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Dusun</label>
                                <input type="text" name="dusun_new" id="field_dusun" class="w-full bg-white border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-500 transition-all" placeholder="Dusun 1">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">RW</label>
                                <input type="text" name="rw_new" id="field_rw" class="w-full bg-white border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-500 transition-all" placeholder="001">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">RT</label>
                                <input type="text" name="rt_new" id="field_rt" class="w-full bg-white border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-slate-500 transition-all" placeholder="001">
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <label class="block text-xs font-bold text-slate-600 mb-2">Atau Pakai Master Wilayah Yang Ada:</label>
                            <select name="rt_id" id="select_master_rt" class="w-full bg-white border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-slate-500 transition-all">
                                <option value="">(Biarkan Sesuai Ketikan Di Atas)</option>
                                @foreach($rws as $rw)
                                    @foreach($rw->rts as $rt)
                                        <option value="{{ $rt->id }}" 
                                                data-rt="{{ $rt->kode }}" 
                                                data-rw="{{ $rw->kode }}" 
                                                data-rw-id="{{ $rw->id }}" 
                                                data-dusun="{{ $rt->dusun->nama ?? '' }}">
                                            RW {{ $rw->kode }} - RT {{ $rt->kode }} ({{ $rt->dusun->nama ?? 'Dusun ?' }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-2 italic">Memilih dari daftar ini akan otomatis mengisi kolom Dusun/RW/RT di atas.</p>
                        </div>
                    </div>

                    <!-- Action Type -->
                    <div class="space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Pilih Keputusan Akhir:</label>
                        
                        <div class="grid grid-cols-1 gap-3" id="actionOptions">
                            <!-- Tombol UPDATE (Utama) -->
                            <label id="opt_update_existing" class="relative flex items-center p-5 rounded-3xl border-2 border-slate-100 cursor-pointer hover:bg-blue-50 transition-all group has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50">
                                <input type="radio" name="action" value="update_existing_from_incoming" class="hidden">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-black text-slate-900">✔️ Update Data Lama</span>
                                        <span class="text-xs text-slate-500">Timpa data lama dengan data baru dari Excel.</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Tombol ABAIKAN -->
                            <label class="relative flex items-center p-5 rounded-3xl border-2 border-slate-100 cursor-pointer hover:bg-slate-100 transition-all group has-[:checked]:border-slate-900 has-[:checked]:bg-slate-50">
                                <input type="radio" name="action" value="skip" class="hidden">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center group-has-[:checked]:bg-slate-900 group-has-[:checked]:text-white transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-black text-slate-900">❌ Abaikan</span>
                                        <span class="text-xs text-slate-500">Pertahankan data lama. Data Excel dibuang.</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Opsi Khusus Wilayah -->
                            <div id="additional_wilayah_actions" class="mt-4 p-4 bg-slate-50 rounded-3xl border border-slate-200 hidden">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center mb-3">Opsi Khusus Wilayah</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="relative flex items-center p-4 rounded-2xl border-2 border-white bg-white cursor-pointer hover:border-emerald-500 transition-all group has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                                        <input type="radio" name="action" value="use_existing" class="hidden">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 leading-tight">Pakai Master RT</span>
                                            <span class="text-[10px] text-slate-500 mt-1">Paksa pakai data Master Wilayah.</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center p-4 rounded-2xl border-2 border-white bg-white cursor-pointer hover:border-purple-500 transition-all group has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                        <input type="radio" name="action" value="create_override" class="hidden">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 leading-tight">Buat RT Baru</span>
                                            <span class="text-[10px] text-slate-500 mt-1">Tambahkan wilayah baru ke sistem.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50 border-t border-slate-100 flex gap-3">
                    <button type="button" onclick="closeResolveModal()" class="flex-1 px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold transition-all hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-slate-900 text-white rounded-2xl font-bold transition-all hover:bg-slate-800 shadow-xl shadow-slate-200">
                        Terapkan Keputusan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>

@endsection

@push('scripts')
<script nonce="{{ $csp_nonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all resolve buttons
        document.querySelectorAll('.btn-resolve-issue').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;
                openResolveModal(
                    data.id, 
                    data.type, 
                    data.nama, 
                    data.nik, 
                    data.nkk, 
                    data.alamat, 
                    data.rt, 
                    data.rw, 
                    data.dusun,
                    data.reason,
                    data.jk,
                    data.existing ? JSON.parse(data.existing) : null
                );
            });
        });

        // Handle Master RT Selection Sync
        const masterSelect = document.getElementById('select_master_rt');
        if (masterSelect) {
            masterSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (selected.value) {
                    // Update manual fields based on selection
                    document.getElementById('field_rt').value = selected.dataset.rt;
                    document.getElementById('field_rw').value = selected.dataset.rw;
                    document.getElementById('field_dusun').value = selected.dataset.dusun;
                    
                    // Update hidden IDs for controller
                    document.getElementById('field_rw_id').value = selected.dataset.rwId;
                    
                    // Auto-select "Pakai Master RT" action for convenience
                    const useExistingRadio = document.querySelector('input[name="action"][value="use_existing"]');
                    if (useExistingRadio) useExistingRadio.checked = true;
                } else {
                    document.getElementById('field_rw_id').value = '';
                }
            });
        }

        // Realtime Validation for NIK & NKK
        const nikInput = document.getElementById('field_nik');
        const nkkInput = document.getElementById('field_nkk');
        
        if (nikInput) {
            nikInput.addEventListener('input', debounce(function() {
                validateNIK(this.value);
            }, 500));
        }
        
        if (nkkInput) {
            nkkInput.addEventListener('input', debounce(function() {
                validateNKK(this.value);
            }, 500));
        }
    });

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    function validateNIK(nik) {
        const errorEl = document.getElementById('nik_error');
        const inputEl = document.getElementById('field_nik');
        
        if (!nik) {
            hideError(errorEl, inputEl);
            return;
        }

        if (nik.length !== 16) {
            showError(errorEl, inputEl, `NIK harus 16 digit (Sekarang: ${nik.length})`, 'text-amber-600');
            return;
        }

        // Check availability via API
        fetch(`/penduduk/check-nik?nik=${nik}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    showError(errorEl, inputEl, `⚠️ NIK sudah terdaftar di database (${data.nama})`, 'text-rose-600');
                } else {
                    showSuccess(errorEl, inputEl, '✓ NIK tersedia');
                }
            })
            .catch(() => hideError(errorEl, inputEl));
    }

    function validateNKK(nkk) {
        const errorEl = document.getElementById('nkk_error');
        const inputEl = document.getElementById('field_nkk');
        
        if (!nkk) {
            hideError(errorEl, inputEl);
            return;
        }

        if (nkk.length !== 16) {
            showError(errorEl, inputEl, `NKK harus 16 digit (Sekarang: ${nkk.length})`, 'text-amber-600');
            return;
        }

        // Check availability via API
        fetch(`/mutasi/check-nkk?nkk=${nkk}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    showSuccess(errorEl, inputEl, '✓ NKK ditemukan (KK sudah ada)');
                } else {
                    showError(errorEl, inputEl, 'ℹ️ NKK baru (akan buat KK baru)', 'text-blue-600');
                }
            })
            .catch(() => hideError(errorEl, inputEl));
    }

    function showError(el, input, msg, colorClass) {
        el.textContent = msg;
        el.className = `mt-1 text-[10px] font-bold ${colorClass}`;
        el.classList.remove('hidden');
        input.classList.add('border-rose-300', 'ring-2', 'ring-rose-100');
        input.classList.remove('border-emerald-300', 'ring-emerald-100');
    }

    function showSuccess(el, input, msg) {
        el.textContent = msg;
        el.className = `mt-1 text-[10px] font-bold text-emerald-600`;
        el.classList.remove('hidden');
        input.classList.remove('border-rose-300', 'ring-rose-100');
        input.classList.add('border-emerald-300', 'ring-2', 'ring-emerald-100');
    }

    function hideError(el, input) {
        el.classList.add('hidden');
        input.classList.remove('border-rose-300', 'ring-rose-100', 'border-emerald-300', 'ring-emerald-100');
    }

    function openResolveModal(id, type, nama, nik, nkk, alamat, rt, rw, dusun, reason, jk, existing) {
        const modal = document.getElementById('resolveModal');
        const content = document.getElementById('modalContent');
        const form = document.getElementById('resolveForm');
        
        // Reset form
        form.reset();
        form.action = `/settings/wilayah/import-conflicts/${id}/resolve`;
        
        // Fill fields
        document.getElementById('field_nama').value = nama || '';
        document.getElementById('field_nik').value = nik || '';
        document.getElementById('field_nkk').value = nkk || '';
        document.getElementById('field_alamat').value = alamat || '';
        document.getElementById('field_rt').value = rt || '';
        document.getElementById('field_rw').value = rw || '';
        document.getElementById('field_dusun').value = dusun || '';
        document.getElementById('modal_reason_text').textContent = reason || 'Tidak ada keterangan masalah.';

        // Handle Comparison Display
        const compSection = document.getElementById('comparison_section');
        if (existing && type === 'nik_conflict') {
            compSection.classList.remove('hidden');
            
            // Fill Old Data
            document.getElementById('compare_old_nama').textContent = existing.nama;
            document.getElementById('compare_old_nik').textContent = existing.nik;
            document.getElementById('compare_old_nkk').textContent = existing.nkk;
            document.getElementById('compare_old_jk').textContent = existing.jk || '-';
            document.getElementById('compare_old_status').textContent = existing.status;
            document.getElementById('compare_old_alamat').textContent = existing.alamat || '-';
            document.getElementById('compare_old_wilayah').textContent = `RW ${existing.rw} / RT ${existing.rt} (${existing.dusun})`;

            // Fill New Data
            document.getElementById('compare_new_nama').textContent = nama;
            document.getElementById('compare_new_nik').textContent = nik;
            document.getElementById('compare_new_nkk').textContent = nkk;
            document.getElementById('compare_new_jk').textContent = jk || '-';
            document.getElementById('compare_new_alamat').textContent = alamat || '-';
            document.getElementById('compare_new_wilayah').textContent = `RW ${rw} / RT ${rt} (${dusun})`;

            // Highlight differences
            const highlight = (oldVal, newVal, el) => {
                if (String(oldVal || '').trim() !== String(newVal || '').trim()) {
                    el.classList.add('text-rose-600', 'underline', 'decoration-rose-300');
                } else {
                    el.classList.remove('text-rose-600', 'underline', 'decoration-rose-300');
                }
            };
            highlight(existing.nama, nama, document.getElementById('compare_new_nama'));
            highlight(existing.nkk, nkk, document.getElementById('compare_new_nkk'));
            highlight(existing.jk, jk, document.getElementById('compare_new_jk'));
            highlight(existing.alamat, alamat, document.getElementById('compare_new_alamat'));
        } else {
            compSection.classList.add('hidden');
        }

        // Filter Actions
        const optUpdate = document.getElementById('opt_update_existing');
        const optWilayah = document.getElementById('additional_wilayah_actions');
        const optUpdateTitle = optUpdate.querySelector('.flex-col span:first-child');
        const optUpdateDesc = optUpdate.querySelector('.flex-col span:last-child');

        if (type === 'nik_conflict') {
            optUpdate.style.display = 'flex';
            optWilayah.style.display = 'none';
            optUpdateTitle.textContent = 'Update Data Lama';
            optUpdateDesc.textContent = 'Timpa data lama dengan data baru dari Excel.';
            optUpdate.querySelector('input').checked = true;
        } else if (type === 'required_field_missing') {
            optUpdate.style.display = 'flex';
            optWilayah.style.display = 'none';
            optUpdateTitle.textContent = 'Perbaiki & Simpan';
            optUpdateDesc.textContent = 'Simpan sebagai penduduk baru setelah data diperbaiki.';
            optUpdate.querySelector('input').checked = true;
        } else if (type === 'wilayah_conflict') {
            optUpdate.style.display = 'none';
            optWilayah.style.display = 'block';
            document.querySelector('input[name="action"][value="create_override"]').checked = true;
        } else {
            optUpdate.style.display = 'flex'; 
            optUpdateTitle.textContent = 'Perbaiki & Proses';
            optUpdateDesc.textContent = 'Gunakan data hasil perbaikan untuk memproses data ini.';
            optWilayah.style.display = 'none';
            document.querySelector('input[name="action"][value="fix_fields"]').checked = true;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeResolveModal() {
        const modal = document.getElementById('resolveModal');
        const content = document.getElementById('modalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
