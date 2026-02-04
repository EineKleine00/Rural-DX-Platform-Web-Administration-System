@extends('layouts.app')

@section('title', 'Laporan Penyebaran Warga')

@section('content')
<div class="container mt-4">
    <!-- Header dengan Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fa-solid fa-chart-pie me-2"></i>Laporan Penyebaran Warga
            </h4>
            <p class="text-muted mb-0">Statistik distribusi warga per RT berdasarkan kategori</p>
        </div>
        <form method="GET" action="{{ route('laporan.index') }}" class="d-flex gap-2">
            <select name="filter" class="form-select" onchange="this.form.submit()">
                <option value="agama" {{ $filter == 'agama' ? 'selected' : '' }}>Agama</option>
                <option value="jenis_kelamin" {{ $filter == 'jenis_kelamin' ? 'selected' : '' }}>Jenis Kelamin</option>
                <option value="status_nikah" {{ $filter == 'status_nikah' ? 'selected' : '' }}>Status Nikah</option>
                <option value="pekerjaan" {{ $filter == 'pekerjaan' ? 'selected' : '' }}>Pekerjaan</option>
                <option value="pendidikan" {{ $filter == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                <option value="usia" {{ $filter == 'usia' ? 'selected' : '' }}>Kelompok Usia</option>
                <option value="kk" {{ $filter == 'kk' ? 'selected' : '' }}>Jumlah KK</option>
            </select>
            <a href="{{ route('laporan.download', ['filter' => $filter]) }}" class="btn btn-danger">
                <i class="fa-solid fa-file-pdf me-1"></i>Download PDF
            </a>
        </form>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">Total Warga</h6>
                    <h3 class="mb-0">{{ $totalWarga ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">Jumlah KK</h6>
                    <h3 class="mb-0">{{ $totalKK ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">Total RT</h6>
                    <h3 class="mb-0">{{ $totalRT ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center py-3">
                    <h6 class="card-title mb-2">Filter Aktif</h6>
                    <h5 class="mb-0 text-capitalize">{{ $filter }}</h5>
                </div>
            </div>
        </div>
    </div>

    @if(empty($chartData))
        <div class="alert alert-warning text-center">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            Tidak ada data untuk ditampilkan.
        </div>
    @else
        <!-- Group by RW -->
        @php
            $groupedByRW = [];
            foreach ($chartData as $key => $data) {
                $rw = $data['rw'];
                if (!isset($groupedByRW[$rw])) {
                    $groupedByRW[$rw] = [];
                }
                $groupedByRW[$rw][] = $data;
            }
        @endphp

        @foreach($groupedByRW as $rw => $rtData)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold py-2 px-3">
                <i class="fa-solid fa-map-location-dot me-2"></i>RW {{ $rw }}
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @foreach($rtData as $data)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="chart-card card border-0 h-100">
                            <div class="card-header bg-success text-white fw-bold text-center py-2">
                                <i class="fa-solid fa-house me-1"></i>RT {{ $data['rt'] }}
                            </div>
                            <div class="card-body p-2">
                                <div class="chart-container">
                                    <canvas id="chart-{{ $data['rw'] }}-{{ $data['rt'] }}"></canvas>
                                </div>
                            </div>
                            <div class="card-footer bg-light text-center py-2">
                                <small class="text-muted fw-bold">
                                    Total: {{ $data['datasets'][0]['total'] }} warga
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        <!-- Tabel Data Detail -->
<div class="card mt-4 border-0 shadow-sm">
    <div class="card-header bg-secondary text-white fw-bold py-2 px-3">
        <i class="fa-solid fa-table me-2"></i>Detail Data per RT
    </div>
    <div class="card-body p-3">
        @foreach($groupedByRW as $rw => $rtData)
        <div class="mb-4">
            <h6 class="text-primary mb-2 fw-bold d-flex align-items-center">
                <i class="fa-solid fa-map-location-dot me-2"></i>RW {{ $rw }}
                <span class="badge bg-primary ms-2">{{ count($rtData) }} RT</span>
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center py-2" style="width: 80px;">RT</th>
                            @php
                                // Ambil semua label unik untuk RW ini
                                $allLabels = [];
                                foreach ($rtData as $data) {
                                    $allLabels = array_merge($allLabels, $data['labels']);
                                }
                                $uniqueLabels = array_unique($allLabels);
                                sort($uniqueLabels);
                            @endphp
                            
                            @foreach($uniqueLabels as $label)
                            <th class="text-center py-2" style="min-width: 100px;">{{ $label }}</th>
                            @endforeach
                            <th class="text-center py-2 bg-light" style="width: 100px;">Total Warga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rtData as $data)
                        <tr>
                            <td class="text-center fw-bold py-2 bg-light">RT {{ $data['rt'] }}</td>
                            
                            @foreach($uniqueLabels as $label)
                            @php
                                $index = array_search($label, $data['labels']);
                                $count = $index !== false ? $data['datasets'][0]['data'][$index] : 0;
                            @endphp
                            <td class="text-center py-2">
                                @if($count > 0)
                                    <span class="fw-semibold">{{ $count }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endforeach
                            
                            <td class="text-center fw-bold text-white py-2 bg-primary">
                                {{ $data['datasets'][0]['total'] }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <td class="text-center fw-bold py-2">Total</td>
                            @foreach($uniqueLabels as $label)
                            @php
                                $totalPerLabel = 0;
                                foreach ($rtData as $data) {
                                    $index = array_search($label, $data['labels']);
                                    if ($index !== false) {
                                        $totalPerLabel += $data['datasets'][0]['data'][$index];
                                    }
                                }
                            @endphp
                            <td class="text-center fw-bold py-2 bg-warning">
                                {{ $totalPerLabel }}
                            </td>
                            @endforeach
                            <td class="text-center fw-bold text-white py-2 bg-success">
                                {{ array_sum(array_column(array_map(fn($d) => $d['datasets'][0], $rtData), 'total')) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            @if(!$loop->last)
            <div class="mt-3 mb-2 border-bottom"></div>
            @endif
        </div>
        @endforeach
    </div>
</div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);

    if (!chartData || Object.keys(chartData).length === 0) {
        console.warn("⚠️ Tidak ada data untuk ditampilkan di grafik.");
        return;
    }

    // Warna yang konsisten untuk semua chart
    const colorPalette = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
        '#FF9F40', '#C9CBCF', '#7CFF7C', '#FF6B6B', '#47D7D7',
        '#F764FF', '#9CFF57', '#FFA857', '#5785FF', '#8C52FF'
    ];

    Object.entries(chartData).forEach(([key, data]) => {
        const ctx = document.getElementById(`chart-${data.rw}-${data.rt}`);
        if (!ctx) return;

        const dataset = data.datasets[0];
        
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    label: dataset.label,
                    data: dataset.data,
                    backgroundColor: data.labels.map((_, index) => 
                        colorPalette[index % colorPalette.length]
                    ),
                    borderColor: '#ffffff',
                    borderWidth: 1.5,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            font: {
                                size: 10
                            }
                        }
                    },
                    title: { 
                        display: false
                    },
                    tooltip: {
                        bodyFont: {
                            size: 11
                        },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 5,
                        bottom: 5,
                        left: 5,
                        right: 5
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 800
                }
            }
        });
    });
});
</script>

<style>
.chart-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.chart-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.chart-container {
    position: relative;
    height: 200px;
    width: 100%;
}

.card-header {
    font-size: 0.9rem;
}

.card-body {
    padding: 0.5rem !important;
}

.card-footer {
    font-size: 0.8rem;
    padding: 0.5rem !important;
}

.table th,
.table td {
    padding: 0.5rem !important;
    font-size: 0.85rem;
}

.table th {
    background-color: #f8f9fa !important;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container {
        height: 180px;
    }
    
    .card-header {
        font-size: 0.85rem;
        padding: 0.4rem 0.5rem !important;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 160px;
    }
    
    .col-md-6 {
        margin-bottom: 1rem;
    }
}

/* Compact spacing */
.row.g-3 {
    margin: -0.5rem;
}

.row.g-3 > [class*="col-"] {
    padding: 0.5rem;
}

/* Smooth animations */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
</style>
@endsection