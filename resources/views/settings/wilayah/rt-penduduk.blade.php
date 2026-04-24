@extends('layouts.app')

@section('title', 'Detail Penduduk RT')
@section('subtitle', 'Daftar penduduk per RT/RW')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl shadow-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Detail Penduduk RT {{ $rt->kode }} / RW {{ $rt->rw?->kode ?? '-' }}</h2>
                <p class="text-green-100 mt-1">Dusun: {{ $rt->dusun?->nama ?? '-' }}</p>
                <p class="text-green-200 text-sm mt-1">Total: <strong>{{ $penduduks->total() }}</strong> orang</p>
            </div>
            <a href="{{ route('settings.wilayah.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-white text-sm font-semibold transition">
                ← Kembali ke Master Wilayah
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border-0 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Penduduk Terhubung</h3>
            <p class="text-sm text-gray-600">Klik tombol detail untuk membuka profil penduduk</p>
        </div>
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-600 uppercase tracking-wider text-xs">
                            <th class="px-4 py-3 text-left font-semibold">Nama</th>
                            <th class="px-4 py-3 text-left font-semibold">NIK</th>
                            <th class="px-4 py-3 text-left font-semibold">NKK</th>
                            <th class="px-4 py-3 text-left font-semibold">Alamat</th>
                            <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penduduks as $p)
                            <tr class="border-t hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $p->nama }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $p->nik }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $p->nkk }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $p->alamat ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('penduduk.show', $p) }}" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-lg text-xs shadow-sm">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada penduduk terhubung pada RT/RW ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $penduduks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
