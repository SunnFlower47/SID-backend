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
                    @can('pelayanan_informasi')
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
                                        'completed' => 'blue',
                                        'diproses' => 'blue',
                                        'selesai' => 'green',
                                        'ditolak' => 'red'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai',
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColors[$suratPengajuan->status] ?? 'gray' }}-100 text-{{ $statusColors[$suratPengajuan->status] ?? 'gray' }}-800">
                                    {{ $statusLabels[$suratPengajuan->status] ?? $suratPengajuan->status }}
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
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ is_numeric($suratPengajuan->jenis_surat) ? 'Deskripsi Keperluan' : 'Keperluan' }}
                                </dt>
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

                <!-- Berkas Lampiran -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border-2 border-orange-200">
                    <div class="px-6 py-4 border-b border-orange-100 bg-orange-50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-orange-800">
                            <i class="fas fa-file-pdf mr-2"></i>Berkas Persyaratan (Warga)
                        </h3>
                    </div>
                    <div class="px-6 py-6 text-center">
                        @if($suratPengajuan->file_lampiran)
                            <div class="mb-4 text-sm text-gray-600 italic">
                                Warga telah mengunggah berkas persyaratan. Silakan cek detailnya di bawah ini:
                            </div>
                            @can('pelayanan_informasi')
                            <a href="{{ asset('storage/' . $suratPengajuan->file_lampiran) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow-lg transition-all hover:scale-105">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Buka Berkas PDF Warga
                            </a>
                            @endcan
                        @else
                            <div class="text-gray-500 italic py-4">
                                <i class="fas fa-info-circle mr-2"></i>Tidak ada berkas yang diunggah oleh warga.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Manual Letter Instruction -->
                @if(is_numeric($suratPengajuan->jenis_surat))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-bold text-blue-800">Instruksi Pengajuan Manual</h4>
                            <p class="text-sm text-blue-700 mt-2 leading-relaxed">
                                Pengajuan ini adalah jenis <strong>Surat Lainnya (Manual)</strong>. Silakan ikuti alur berikut:
                            </p>
                            <ul class="text-sm text-blue-700 mt-3 space-y-2 list-disc list-inside">
                                <li><strong>Salin Data:</strong> Copy NIK & Nama warga dari panel sebelah kanan.</li>
                                <li><strong>Cek Berkas:</strong> Klik tombol oranye di atas untuk melihat persyaratan yang diunggah warga.</li>
                                <li><strong>Buat Surat:</strong> Gunakan aplikasi <strong>Microsoft Word</strong> Anda untuk membuat surat ini secara manual.</li>
                                <li><strong>Update Status:</strong> Setelah surat dicetak dan diberikan ke warga, ubah status menjadi <span class="font-bold text-blue-800">"Selesai"</span>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

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
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(is_array($value))
                                        <ul class="list-disc list-inside">
                                            @foreach($value as $v)
                                                <li>{{ is_array($v) ? json_encode($v) : $v }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
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
                @can('pelayanan_informasi')
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
                                    <option value="diproses" {{ $suratPengajuan->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="ditolak" {{ $suratPengajuan->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    <option value="selesai" {{ $suratPengajuan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
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

