@extends('layouts.app')

@section('title', 'Detail Testimoni')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Testimoni</h1>
            <p class="text-muted mb-0">Lihat detail testimoni dari warga</p>
        </div>
        <a href="{{ route('testimoni.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Testimoni Detail -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Testimoni dari {{ $testimoni->nama }}</h6>
                    <div>
                        @if($testimoni->status == 'approved')
                            <span class="badge bg-success">Disetujui</span>
                        @elseif($testimoni->status == 'pending')
                            <span class="badge bg-warning">Menunggu</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Nama Lengkap</label>
                            <p class="text-gray-800">{{ $testimoni->nama }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">RT/RW</label>
                            <p class="text-gray-800">{{ $testimoni->rt_rw }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Rating</label>
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $testimoni->rating ? 'text-warning' : 'text-gray-300' }} fa-lg me-1"></i>
                            @endfor
                            <span class="ms-3 font-weight-bold text-gray-800">{{ $testimoni->rating }}/5</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Testimoni</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-800 mb-0">"{{ $testimoni->testimoni }}"</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Tanggal Dibuat</label>
                            <p class="text-gray-800">{{ $testimoni->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">IP Address</label>
                            <p class="text-gray-800">{{ $testimoni->ip_address ?? 'Tidak tersedia' }}</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('testimoni.edit', $testimoni) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        @if($testimoni->status == 'pending')
                            <form action="{{ route('testimoni.approve', $testimoni) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui testimoni ini?')">
                                    <i class="fas fa-check me-2"></i>Setujui
                                </button>
                            </form>
                            <form action="{{ route('testimoni.reject', $testimoni) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak testimoni ini?')">
                                    <i class="fas fa-times me-2"></i>Tolak
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('testimoni.destroy', $testimoni) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus testimoni ini?')">
                                <i class="fas fa-trash me-2"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

