@extends('layouts.app')

@section('title', 'Import Issue Queue')
@section('subtitle', 'Queue issue import yang perlu diproses')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl shadow-xl p-6 text-white">
        <h1 class="text-2xl font-bold">Import Issue Queue</h1>
        <p class="text-red-100 mt-1">Kelola semua issue import: konflik wilayah, NIK conflict, dan invalid required field.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="text-sm text-gray-600">Batch ID</label>
                <input type="text" name="batch_id" value="{{ request('batch_id') }}" class="w-full border rounded-lg px-3 py-2" placeholder="imp-...">
            </div>
            <div>
                <label class="text-sm text-gray-600">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="resolved" {{ request('status')==='resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Issue Type</label>
                <select name="issue_type" class="w-full border rounded-lg px-3 py-2">
                    <option value="all">Semua</option>
                    <option value="wilayah_conflict" {{ request('issue_type')==='wilayah_conflict' ? 'selected' : '' }}>wilayah_conflict</option>
                    <option value="nik_conflict" {{ request('issue_type')==='nik_conflict' ? 'selected' : '' }}>nik_conflict</option>
                    <option value="required_field_missing" {{ request('issue_type')==='required_field_missing' ? 'selected' : '' }}>required_field_missing</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-gray-800 text-white rounded-lg">Filter</button>
                <a href="{{ route('settings.wilayah.import-conflicts.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">Reset</a>
            </div>
        </form>
    </div>

    @php
        $rows = $conflicts->getCollection();
        $countPending = $rows->where('status', 'pending')->count();
        $countResolved = $rows->where('status', 'resolved')->count();
        $countFailed = $rows->where('reprocess_status', 'failed')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="text-xs uppercase text-amber-700 font-semibold">Pending</div>
            <div class="text-2xl font-bold text-amber-900 mt-1">{{ $countPending }}</div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
            <div class="text-xs uppercase text-emerald-700 font-semibold">Resolved</div>
            <div class="text-2xl font-bold text-emerald-900 mt-1">{{ $countResolved }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="text-xs uppercase text-red-700 font-semibold">Reprocess Failed</div>
            <div class="text-2xl font-bold text-red-900 mt-1">{{ $countFailed }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr class="text-xs uppercase text-gray-600">
                        <th class="px-4 py-3 text-left">Batch/Row</th>
                        <th class="px-4 py-3 text-left">Identitas</th>
                        <th class="px-4 py-3 text-left">Raw Wilayah</th>
                        <th class="px-4 py-3 text-left">Issue Type</th>
                        <th class="px-4 py-3 text-left">Reason</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conflicts as $c)
                        <tr class="border-t align-top hover:bg-gray-50/70">
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $c->batch_id }}<br>#{{ $c->row_number }}</td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-semibold text-sm text-gray-800">{{ $c->nama ?: '-' }}</div>
                                <div class="mt-1"><span class="font-semibold text-gray-600">NIK:</span> <span class="font-mono">{{ $c->nik ?: '-' }}</span></div>
                                <div><span class="font-semibold text-gray-600">NKK:</span> <span class="font-mono">{{ $c->nkk ?: '-' }}</span></div>
                            </td>
                            <td class="px-4 py-3 text-xs">RW {{ $c->rw_raw }} / RT {{ $c->rt_raw }}<br>Dusun: {{ $c->dusun_raw ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs">
                                @php
                                    $type = $c->issue_type ?: 'wilayah_conflict';
                                    $typeLabel = $type === 'wilayah_conflict' ? 'Konflik Wilayah' : ($type === 'nik_conflict' ? 'Konflik NIK' : 'Field Wajib Kurang');
                                    $typeClass = $type === 'wilayah_conflict' ? 'bg-blue-100 text-blue-700' : ($type === 'nik_conflict' ? 'bg-purple-100 text-purple-700' : 'bg-rose-100 text-rose-700');
                                @endphp
                                <span class="px-2 py-1 rounded-full {{ $typeClass }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="bg-red-50 border border-red-200 rounded-lg p-2 text-red-700">{{ $c->reason }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs space-y-1">
                                <span class="px-2 py-1 rounded-full text-xs {{ $c->status==='pending' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">{{ strtoupper($c->status) }}</span>
                                @if($c->status === 'resolved')
                                    <div>
                                        <span class="px-2 py-1 rounded-full text-[10px] {{ ($c->reprocess_status ?? 'pending') === 'failed' ? 'bg-red-100 text-red-700' : (($c->reprocess_status ?? 'pending') === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700') }}">
                                            REPROCESS: {{ strtoupper($c->reprocess_status ?? 'pending') }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $canEdit = $c->status === 'pending' || ($c->status === 'resolved' && ($c->reprocess_status ?? null) === 'failed');
                                @endphp
                                @if($canEdit)
                                    <div class="space-y-2">
                                        @if($c->status === 'resolved' && ($c->reprocess_status ?? null) === 'failed')
                                            <div class="text-[11px] text-red-700 bg-red-50 border border-red-200 rounded p-2">
                                                Reprocess sebelumnya gagal. Silakan perbaiki field/resolution lalu submit ulang.
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="flex gap-2">
                                            @csrf
                                            <input type="hidden" name="action" value="skip">
                                            <button class="w-full px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded text-xs">Mark Resolved / Skip</button>
                                        </form>

                                        @if(($c->issue_type ?? 'wilayah_conflict') === 'wilayah_conflict')
                                            <div class="text-[11px] font-semibold text-blue-700 uppercase">Aksi Konflik Wilayah</div>
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="action" value="create_override">
                                                <button class="w-full px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs">Create Override</button>
                                            </form>

                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="space-y-1">
                                                @csrf
                                                <input type="hidden" name="action" value="use_existing">
                                                <select name="rw_id" class="border rounded px-2 py-1 text-xs w-full" required>
                                                    <option value="">Pilih RW</option>
                                                    @foreach($rws as $rw)
                                                        <option value="{{ $rw->id }}">RW {{ $rw->kode }}</option>
                                                    @endforeach
                                                </select>
                                                <select name="rt_id" class="border rounded px-2 py-1 text-xs w-full" required>
                                                    <option value="">Pilih RT existing</option>
                                                    @foreach($rws as $rw)
                                                        @foreach($rw->rts as $rt)
                                                            <option value="{{ $rt->id }}">RW {{ $rw->kode }} - RT {{ $rt->kode }}</option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                <button class="w-full px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs">Use Existing</button>
                                            </form>
                                        @endif

                                        @if(($c->issue_type ?? '') === 'nik_conflict')
                                            <div class="text-[11px] font-semibold text-purple-700 uppercase">Aksi Konflik NIK</div>
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="action" value="keep_existing_nik">
                                                <button class="w-full px-3 py-1.5 bg-gray-700 hover:bg-gray-800 text-white rounded text-xs">Keep Existing NIK</button>
                                            </form>
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="flex gap-2">
                                                @csrf
                                                <input type="hidden" name="action" value="update_existing_from_incoming">
                                                <button class="w-full px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs">Update Existing</button>
                                            </form>
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="space-y-1">
                                                @csrf
                                                <input type="hidden" name="action" value="change_incoming_nik">
                                                <input type="text" name="nik_new" class="border rounded px-2 py-1 text-xs w-full" placeholder="NIK baru" required>
                                                <button class="w-full px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs">Change Incoming NIK</button>
                                            </form>
                                        @endif

                                        @if(in_array(($c->issue_type ?? ''), ['required_field_missing']))
                                            <div class="text-[11px] font-semibold text-rose-700 uppercase">Perbaikan Field Wajib</div>
                                            <form method="POST" action="{{ route('settings.wilayah.import-conflicts.resolve', $c) }}" class="space-y-2 text-xs">
                                                @csrf
                                                <input type="hidden" name="action" value="fix_fields">

                                                <div>
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">NIK</label>
                                                    <input type="text" name="nik_new" class="border rounded px-2 py-1 text-xs w-full" placeholder="Contoh: 3201..." value="{{ $c->nik }}">
                                                </div>

                                                <div>
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama</label>
                                                    <input type="text" name="nama_new" class="border rounded px-2 py-1 text-xs w-full" placeholder="Nama lengkap" value="{{ $c->nama }}">
                                                </div>

                                                <div>
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">NKK</label>
                                                    <input type="text" name="nkk_new" class="border rounded px-2 py-1 text-xs w-full" placeholder="Contoh: 3201..." value="{{ $c->nkk }}">
                                                </div>

                                                <button class="w-full px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs">Fix Fields</button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="space-y-1 text-xs">
                                        <span class="text-gray-600">Issue final.</span>
                                        <div class="text-[11px] text-gray-500">Reprocess: {{ $c->reprocess_status ?? 'pending' }}</div>
                                        @if(!empty($c->reprocess_message))
                                            <div class="text-[11px] text-gray-500">{{ $c->reprocess_message }}</div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada issue import.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $conflicts->links() }}</div>
    </div>
</div>
@endsection
