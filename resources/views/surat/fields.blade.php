@extends('layouts.app')

@section('title', 'Available Fields untuk Template')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fas fa-list-alt me-2"></i>Available Fields untuk Template Word
            </h4>
            <p class="text-muted mb-0">Daftar semua field yang bisa digunakan di template Word</p>
        </div>
        <div>
            <a href="{{ route('surat.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Buat Surat
            </a>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Cara Penggunaan:</strong> Gunakan field dengan format <code>{nama_field}</code> di template Word Anda
    </div>

    @foreach($fields as $category => $fieldList)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-folder me-2"></i>
                {{ $category }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($fieldList as $field)
                <div class="col-md-4 mb-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body py-3">
                            <code class="fs-6">{{ $field }}</code>
                            <div class="mt-1">
                                <small class="text-muted">
                                    @if(str_contains($category, 'MASA BERLAKU'))
                                    ⏰ 90 hari dari tanggal surat
                                    @elseif(str_contains($category, 'NOMOR SURAT'))
                                    🔢 Format: 301/1/X/2025
                                    @elseif(str_contains($category, 'TANGGAL'))
                                    📅 {{ now()->translatedFormat('d F Y') }}
                                    @else
                                    📋 Data dari database
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <!-- Contoh Template -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-word me-2"></i>
                Contoh Template Word
            </h5>
        </div>
        <div class="card-body">
            <pre class="bg-dark text-light p-3 rounded">
<code>==============================================
SURAT KETERANGAN
Nomor: <span class="text-warning">{nomor_surat}</span>
==============================================

Yang bertanda tangan di bawah ini:

Nama    : <span class="text-warning">{kepala_desa}</span>
Jabatan : <span class="text-warning">{jabatan_kepala_desa}</span>

Menerangkan bahwa:

Nama            : <span class="text-warning">{nama}</span>
NIK             : <span class="text-warning">{nik}</span>
Tempat/Tgl Lahir: <span class="text-warning">{tempat_lahir}</span>, <span class="text-warning">{tanggal_lahir}</span>
Alamat          : <span class="text-warning">{alamat}</span>
RT/RW           : <span class="text-warning">{rt}</span>/<span class="text-warning">{rw}</span>

Adalah benar warga <span class="text-warning">{nama_desa}</span>.

Masa berlaku: <span class="text-warning">{tanggal}</span> - <span class="text-warning">{berlaku_hingga}</span>

Dikeluarkan di <span class="text-warning">{nama_desa}</span>
Pada tanggal <span class="text-warning">{tanggal}</span>

<span class="text-warning">{kepala_desa}</span>
<span class="text-warning">{jabatan_kepala_desa}</span></code>
            </pre>
        </div>
    </div>
</div>
@endsection