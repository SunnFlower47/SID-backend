@extends('layouts.app')

@section('title', 'Detail Surat Pengajuan')
@section('subtitle', 'Lihat detail pengajuan surat')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Detail Surat Pengajuan</h2>
                    <p class="text-gray-600 mt-2">Nomor: {{ $suratPengajuan->nomor_surat }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.surat-pengajuan.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    @can('surat.view')
                    <a href="{{ route('admin.surat-pengajuan.preview', $suratPengajuan) }}" target="_blank" class="btn-primary">
                        <i class="fas fa-eye mr-2"></i>
                        Preview Surat
                    </a>
                    <a href="{{ route('admin.surat-pengajuan.pdf', $suratPengajuan) }}" class="btn-success">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Card -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Status Pengajuan</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'yellow',
                                        'approved' => 'green',
                                        'rejected' => 'red',
                                        'completed' => 'blue'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColors[$suratPengajuan->status] }}-100 text-{{ $statusColors[$suratPengajuan->status] }}-800">
                                    {{ $statusLabels[$suratPengajuan->status] }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $suratPengajuan->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surat Information -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Surat</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Surat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->getSuratTypeNameAttribute() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nomor Surat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->nomor_surat }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Surat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->tanggal_surat->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Keperluan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->keperluan ?? '-' }}</dd>
                            </div>
                            @if($suratPengajuan->tujuan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Tujuan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->tujuan }}</dd>
                            </div>
                            @endif
                            @if($suratPengajuan->keterangan_tambahan)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Keterangan Tambahan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->keterangan_tambahan }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Data Tambahan -->
                @if($suratPengajuan->data_tambahan && is_array($suratPengajuan->data_tambahan) && count($suratPengajuan->data_tambahan) > 0)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Data Tambahan</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($suratPengajuan->data_tambahan as $key => $value)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $value }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Pengaju Information -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Data Pengaju</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->nama_pengaju }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->nik_pengaju }}</dd>
                            </div>
                            @if($suratPengajuan->email_pengaju)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->email_pengaju }}</dd>
                            </div>
                            @endif
                            @if($suratPengajuan->no_hp_pengaju)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. HP</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->no_hp_pengaju }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Penduduk Information -->
                @if($suratPengajuan->penduduk)
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Data Penduduk</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->penduduk->nama }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">NIK</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->penduduk->nik }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->penduduk->jenis_kelamin_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $suratPengajuan->penduduk->alamat_lengkap }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @endif

                <!-- Admin Actions -->
                @can('surat.edit')
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Aksi Admin</h3>
                    </div>
                    <div class="px-6 py-4">
                        <form action="{{ route('admin.surat-pengajuan.update-status', $suratPengajuan) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Ubah Status</label>
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="pending" {{ $suratPengajuan->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="approved" {{ $suratPengajuan->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ $suratPengajuan->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="completed" {{ $suratPengajuan->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>

                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Keterangan tambahan...">{{ $suratPengajuan->keterangan }}</textarea>
                            </div>

                            <button type="submit" class="w-full btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
