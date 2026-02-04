@extends('layouts.app')

@section('title', 'Monitor Surat')
@section('page-title', 'Monitor Surat')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fa-solid fa-chart-line me-2"></i>Monitor Nomor Surat
            </h4>
            <p class="text-muted mb-0">Statistik dan monitoring pembuatan surat</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('surat.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-file-alt me-1"></i>Buat Surat
            </a>
            <a href="{{ route('surat.fields') }}" class="btn btn-outline-info">
                <i class="fa-solid fa-code me-1"></i>Template Fields
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fa-solid fa-file-lines fa-2x me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">Total Surat</h6>
                            <h3 class="mb-0">{{ number_format($statistik['total_surat'] ?? 0) }}</h3>
                        </div>
                    </div>
                    <small>Seluruh waktu</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fa-solid fa-calendar-check fa-2x me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">Bulan Ini</h6>
                            <h3 class="mb-0">{{ number_format(array_sum($statistik['bulan_ini'] ?? [])) }}</h3>
                        </div>
                    </div>
                    <small>{{ now()->translatedFormat('F Y') }}</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fa-solid fa-chart-bar fa-2x me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">Tahun Ini</h6>
                            <h3 class="mb-0">{{ number_format(array_sum($statistik['tahun_ini'] ?? [])) }}</h3>
                        </div>
                    </div>
                    <small>Tahun {{ date('Y') }}</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="fa-solid fa-star fa-2x me-3"></i>
                        <div>
                            <h6 class="card-title mb-1">Terbanyak</h6>
                            <h4 class="mb-0">{{ $statistik['jenis_terbanyak'] ?? '-' }}</h4>
                        </div>
                    </div>
                    <small>Jenis Surat</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistik Bulan Ini -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold py-2">
                    <i class="fa-solid fa-chart-pie me-2"></i>Statistik Bulan Ini
                    <small class="float-end">{{ now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="card-body">
                    @if(empty($statistikBulanIni))
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada surat dibuat bulan ini</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Jenis Surat</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Nomor Terakhir</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statistikBulanIni as $data)
                                    <tr>
                                        <td class="fw-bold">
                                            <span class="badge bg-secondary">{{ $data['kode_surat'] }}</span>
                                        </td>
                                        <td>{{ $data['nama_template'] }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $data['counter'] }}</td>
                                        <td class="text-center">
                                            <code class="text-success">{{ $data['nomor_terakhir'] }}</code>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('surat.reset-counter') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="kode" value="{{ $data['kode_surat'] }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Reset counter {{ $data['kode_surat'] }}?')">
                                                    <i class="fa-solid fa-rotate"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="2" class="fw-bold text-end">Total:</td>
                                        <td class="text-center fw-bold text-success">
                                            {{ array_sum(array_column($statistikBulanIni, 'counter')) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik Tahun Ini -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white fw-bold py-2">
                    <i class="fa-solid fa-chart-bar me-2"></i>Statistik Tahun Ini
                    <small class="float-end">Tahun {{ date('Y') }}</small>
                </div>
                <div class="card-body">
                    @if(empty($statistik['tahun_ini']))
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada surat dibuat tahun ini</p>
                        </div>
                    @else
                        <canvas id="chartTahunIni" height="250"></canvas>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Bulanan -->
    @if(!empty($statistik['trend_bulanan']))
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white fw-bold py-2">
                    <i class="fa-solid fa-trend-up me-2"></i>Trend Bulanan
                </div>
                <div class="card-body">
                    <canvas id="chartTrend" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Daftar Template Tersedia -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold py-2">
            <i class="fa-solid fa-list me-2"></i>Daftar Jenis Surat Tersedia
            <span class="badge bg-light text-dark ms-2">{{ $uniqueTemplates->count() }} Jenis</span>
        </div>
        <div class="card-body">
            @if($uniqueTemplates->count() > 0)
                <div class="row">
                    @foreach($uniqueTemplates->groupBy('kategori') as $kategori => $items)
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fa-solid fa-folder me-1"></i>{{ $kategori }}
                            <span class="badge bg-primary ms-2">{{ $items->count() }}</span>
                        </h6>
                        <div class="list-group">
                            @foreach($items as $template)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary me-2">{{ $template->nomor_surat }}</span>
                                    {{ $template->nama_template }}
                                </div>
                                <small class="text-muted">{{ $template->deskripsi }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                    <p>Tidak ada template surat tersedia</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informasi Sistem -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold py-2">
                    <i class="fa-solid fa-circle-info me-2"></i>Informasi Sistem
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Format Nomor Surat</h6>
                            <div class="alert alert-light border">
                                <code class="fs-6">KODE_SURAT / NOMOR_URUT / BULAN_ROMAWI / TAHUN</code><br>
                                <small class="text-muted">Contoh: <strong>301/1/X/2025</strong></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Keterangan</h6>
                            <ul class="list-unstyled">
                                <li><i class="fa-solid fa-rotate text-danger me-2"></i> Reset counter hanya untuk bulan berjalan</li>
                                <li><i class="fa-solid fa-arrow-right-arrow-left me-2"></i> Counter reset otomatis setiap bulan</li>
                                <li><i class="fa-solid fa-database me-2"></i> Data disimpan dalam file counter</li>
                                <li><i class="fa-solid fa-code me-2"></i> Setiap jenis surat memiliki counter terpisah</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Statistik Tahun Ini
    const tahunIniData = @json($statistik['tahun_ini'] ?? []);
    if (Object.keys(tahunIniData).length > 0) {
        const ctxTahun = document.getElementById('chartTahunIni');
        new Chart(ctxTahun, {
            type: 'bar',
            data: {
                labels: Object.keys(tahunIniData),
                datasets: [{
                    label: 'Jumlah Surat',
                    data: Object.values(tahunIniData),
                    backgroundColor: '#28a745',
                    borderColor: '#1e7e34',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Surat per Jenis'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Chart Trend Bulanan
    const trendData = @json($statistik['trend_bulanan'] ?? []);
    if (Object.keys(trendData).length > 0) {
        const ctxTrend = document.getElementById('chartTrend');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: Object.keys(trendData),
                datasets: [{
                    label: 'Total Surat per Bulan',
                    data: Object.values(trendData),
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderColor: '#0d6efd',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>

<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.table th {
    background-color: #f8f9fa !important;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.list-group-item {
    padding: 0.75rem 1rem;
    border: 1px solid rgba(0,0,0,0.125);
    margin-bottom: -1px;
}
</style>
@endsection