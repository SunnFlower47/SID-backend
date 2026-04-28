@extends('layouts.app')

@section('title', 'Tambah Mutasi')
@section('subtitle', 'Tambah data mutasi penduduk')

@section('content')
<div class="space-y-6">
    @include('mutasi.partials.header')
    @include('mutasi.partials.form-container')
                </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/mutasi/global-functions.js') }}?v={{ filemtime(public_path('js/mutasi/global-functions.js')) }}"></script>
<script src="{{ asset('js/mutasi/validation.js') }}?v={{ filemtime(public_path('js/mutasi/validation.js')) }}"></script>
<script src="{{ asset('js/mutasi/form-handlers.js') }}?v={{ filemtime(public_path('js/mutasi/form-handlers.js')) }}"></script>
<script src="{{ asset('js/mutasi/sweetalert.js') }}?v={{ filemtime(public_path('js/mutasi/sweetalert.js')) }}"></script>
@endpush

