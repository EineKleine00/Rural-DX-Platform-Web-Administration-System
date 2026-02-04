@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <div class="d-none d-sm-inline-block">
            <span class="badge bg-primary">
                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }} ({{ ucfirst($userRole) }})
            </span>
            <span class="badge bg-success ms-2">
                <i class="fas fa-clock me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Warga Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Warga</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalWarga) }}</div>
                            <div class="mt-2 text-xs">
                                <span class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['warga_bulan_ini'] }} bulan ini
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Surat Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Template Surat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTemplates }}</div>
                            <div class="mt-2 text-xs">
                                <span class="text-info">
                                    <i class="fas fa-file-alt me-1"></i>{{ $suratStatistik['bulan_ini'] }} dibuat
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Keluarga Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kartu Keluarga</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['kk_total'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="text-warning">
                                    <i class="fas fa-home me-1"></i>Rata² {{ $stats['rata_keluarga'] }} orang/KK
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Surat Dibuat Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Surat Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suratStatistik['bulan_ini'] }}</div>
                            <div class="mt-2">
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $suratStatistik['progress'] }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $suratStatistik['progress'] }}% dari target</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Area Chart - Pertumbuhan Penduduk -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Pertumbuhan Penduduk 6 Bulan Terakhir
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">6 Bulan</a></li>
                            <li><a class="dropdown-item" href="#">1 Tahun</a></li>
                            <li><a class="dropdown-item" href="#">2 Tahun</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="pertumbuhanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Jenis Surat -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi Jenis Surat Bulan Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="jenisSuratChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($jenisSuratData['labels'] as $index => $label)
                        <span class="me-3">
                            <i class="fas fa-circle" style="color: {{ $jenisSuratData['colors'][$index] }}"></i>
                            {{ $label }} ({{ $jenisSuratData['data'][$index] }})
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Quick Actions & User Online -->
        <div class="col-lg-6">
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('surat.index') }}" class="btn btn-primary btn-block h-100 py-3">
                                        <i class="fas fa-envelope fa-2x mb-2"></i><br>
                                        Buat Surat
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('warga.index') }}" class="btn btn-success btn-block h-100 py-3">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>
                                        Data Warga
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    @if($isAdmin)
                                    <a href="{{ route('warga.create') }}" class="btn btn-info btn-block h-100 py-3">
                                        <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                        Tambah Warga
                                    </a>
                                    @else
                                    <button class="btn btn-info btn-block h-100 py-3" disabled>
                                        <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                        Tambah Warga
                                    </button>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('laporan.index') }}" class="btn btn-warning btn-block h-100 py-3">
                                        <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                        Lihat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Online (Hanya untuk Admin) -->
                @if($isAdmin && count($onlineUsers) > 0)
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-check me-2"></i>User Online
                                <span class="badge bg-success ms-2">{{ count($onlineUsers) }}</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($onlineUsers as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-circle text-success me-2"></i>
                                        <strong>{{ $user->name }}</strong>
                                        <small class="text-muted ms-2">({{ $user->role }})</small>
                                    </div>
                                    <form action="{{ route('dashboard.force-logout', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Force logout {{ $user->name }}?')">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity & Warga Baru -->
        <div class="col-lg-6">
            <div class="row">
                <!-- Recent Activity -->
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($recentActivities as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-{{ $activity['color'] }}">
                                        <i class="{{ $activity['icon'] }} text-white"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <p class="mb-1">{{ $activity['text'] }}</p>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warga Baru -->
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-plus me-2"></i>Warga Terbaru
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($newWargas as $warga)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $warga->nama }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-id-card me-1"></i>{{ $warga->nik }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            RT {{ $warga->rt }}/RW {{ $warga->rw }}
                                        </small>
                                        <small class="text-success">
                                            hallo
                                        </small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if($newWargas->count() == 0)
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-3"></i>
                                <p>Tidak ada warga baru</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Demografi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-primary">{{ round(($stats['warga_pria']/$totalWarga)*100, 1) }}%</div>
                            <small class="text-muted">Pria</small>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-primary" style="width: {{ ($stats['warga_pria']/$totalWarga)*100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-success">{{ round(($stats['warga_wanita']/$totalWarga)*100, 1) }}%</div>
                            <small class="text-muted">Wanita</small>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-success" style="width: {{ ($stats['warga_wanita']/$totalWarga)*100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-info">{{ round(($stats['warga_aktif']/$totalWarga)*100, 1) }}%</div>
                            <small class="text-muted">Aktif</small>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-info" style="width: {{ ($stats['warga_aktif']/$totalWarga)*100 }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-warning">{{ $stats['kk_total'] }}</div>
                            <small class="text-muted">KK</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-danger">{{ $stats['warga_hari_ini'] }}</div>
                            <small class="text-muted">Baru Hari Ini</small>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="h4 font-weight-bold text-secondary">{{ $stats['rata_keluarga'] }}</div>
                            <small class="text-muted">Rata²/Keluarga</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pertumbuhan Penduduk Chart
    const ctxPertumbuhan = document.getElementById('pertumbuhanChart');
    if (ctxPertumbuhan) {
        new Chart(ctxPertumbuhan, {
            type: 'line',
            data: {
                labels: @json($pertumbuhanPenduduk['labels']),
                datasets: [{
                    label: 'Total Warga',
                    lineTension: 0.3,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($pertumbuhanPenduduk['data'])
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgb(234, 236, 244)"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyColor: '#858796',
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1
                    }
                }
            }
        });
    }

    // Jenis Surat Pie Chart
    const ctxJenisSurat = document.getElementById('jenisSuratChart');
    if (ctxJenisSurat) {
        new Chart(ctxJenisSurat, {
            type: 'doughnut',
            data: {
                labels: @json($jenisSuratData['labels']),
                datasets: [{
                    data: @json($jenisSuratData['data']),
                    backgroundColor: @json($jenisSuratData['colors']),
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>

<style>
.card {
    border: none;
    border-radius: 0.75rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-bottom: 1px solid #e3e6f0;
    background-color: #f8f9fc;
}

.chart-area {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 250px;
    width: 100%;
}

/* Timeline Styling */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    padding-left: 1rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.timeline-content {
    padding-bottom: 1rem;
    border-left: 2px solid #e3e6f0;
    padding-left: 1rem;
}

.timeline-item:last-child .timeline-content {
    border-left: 2px solid transparent;
}

/* Progress bar styling */
.progress {
    height: 0.5rem;
    border-radius: 0.5rem;
}

.progress-bar {
    border-radius: 0.5rem;
}

/* Button styling */
.btn-block {
    width: 100%;
}

.text-xs {
    font-size: 0.7rem;
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* List group styling */
.list-group-item {
    border: none;
    border-bottom: 1px solid #e3e6f0;
    padding: 1rem 0;
}

.list-group-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .card .card-body {
        padding: 1rem;
    }
    
    .btn-block {
        padding: 0.75rem 0.5rem;
    }
}
</style>
@endsection