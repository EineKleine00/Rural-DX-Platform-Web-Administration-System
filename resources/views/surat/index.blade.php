@extends('layouts.app')

@section('title', 'Buat Surat untuk Warga')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fas fa-file-alt me-2"></i>Buat Surat untuk Warga
            </h4>
            <p class="text-muted mb-0">Pilih warga untuk membuat surat keterangan - 10 KK per halaman</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-home me-1"></i>
            Total: <strong>{{ $kkPaginator->total() }}</strong> Kartu Keluarga
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('surat.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari Warga:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               class="form-control border-start-0" 
                               placeholder="Cari berdasarkan nama, NIK, alamat, no KK...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('surat.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Monitor Link -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            <strong>Sistem Nomor Surat Otomatis</strong> - Setiap jenis surat memiliki nomor urut sendiri
        </div>
        <a href="{{ route('surat.monitor') }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-chart-bar me-1"></i>Monitor Nomor Surat
        </a>
    </div>

    <!-- Results Info -->
    @if($kkPaginator->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Menampilkan <strong>{{ $kkPaginator->firstItem() ?: 0 }} - {{ $kkPaginator->lastItem() ?: 0 }}</strong> 
            dari <strong>{{ $kkPaginator->total() }}</strong> Kartu Keluarga
        </div>
        <div class="text-muted">
            Halaman <strong>{{ $kkPaginator->currentPage() }}</strong> dari {{ $kkPaginator->lastPage() }}
        </div>
    </div>
    @endif

    <!-- KK List -->
    @foreach($kkPaginator as $no_kk => $anggotaKeluarga)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-home me-2"></i>
                        Kartu Keluarga: <strong>{{ $no_kk ?? 'TIDAK ADA KK' }}</strong>
                    </h5>
                    <small class="opacity-75">
                        {{ $anggotaKeluarga->first()->alamat }} - 
                        RT {{ $anggotaKeluarga->first()->rt }}/RW {{ $anggotaKeluarga->first()->rw }}
                    </small>
                </div>
                <span class="badge bg-light text-dark fs-6">
                    {{ $anggotaKeluarga->count() }} Anggota
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th width="120">Hubungan</th>
                            <th width="100">Jenis Kelamin</th>
                            <th>Pekerjaan</th>
                            <th width="100">Status</th>
                            <th width="120" class="text-center">Aksi Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anggotaKeluarga as $index => $item)
                        @php
                            $hubungan = $item->status_hubungan_dalam_keluarga;
                        @endphp
                        <tr class="@if($item->status_hidup == 'Meninggal') table-danger @endif">
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <code>{{ $item->nik }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <strong class="{{ $hubungan === 'Kepala Keluarga' ? 'text-primary' : '' }}">
                                        {{ $item->nama }}
                                        @if($hubungan === 'Kepala Keluarga')
                                            <i class="fas fa-crown text-warning ms-1" title="Kepala Keluarga"></i>
                                        @endif
                                    </strong>
                                    @if($item->status_hidup == 'Meninggal')
                                    <span class="badge bg-danger ms-2">
                                        <i class="fas fa-cross me-1"></i>Meninggal
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($hubungan == 'Kepala Keluarga') bg-success
                                    @elseif($hubungan == 'Suami' || $hubungan == 'Istri') bg-primary
                                    @else bg-info @endif">
                                    {{ $hubungan }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item->jenis_kelamin == 'Laki-laki' ? 'bg-primary' : 'bg-pink' }}">
                                    {{ $item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $item->pekerjaan ?? 'Tidak bekerja' }}</small>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($item->status_nikah == 'Kawin') bg-success
                                    @elseif($item->status_nikah == 'Belum Kawin') bg-info
                                    @elseif($item->status_nikah == 'Cerai') bg-warning
                                    @else bg-secondary @endif">
                                    {{ $item->status_nikah }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm open-modal"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama }}">
                                    <i class="fas fa-file-alt me-1"></i>Buat Surat
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Empty State -->
    @if($kkPaginator->count() == 0)
    <div class="text-center mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">
                    @if(request('search'))
                    Tidak ada Kartu Keluarga ditemukan
                    @else
                    Belum ada data Kartu Keluarga
                    @endif
                </h5>
                <p class="text-muted mb-3">
                    @if(request('search'))
                    Tidak ditemukan KK untuk pencarian: "{{ request('search') }}"
                    @else
                    Data Kartu Keluarga akan ditampilkan di sini
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Pagination - Compact Version -->
    @if($kkPaginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted small">
            {{ $kkPaginator->count() }} KK per halaman
        </div>
        
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <!-- Previous Page -->
                @if($kkPaginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                <!-- Dynamic Page Numbers - Max 5 pages -->
                @php
                    $current = $kkPaginator->currentPage();
                    $last = $kkPaginator->lastPage();
                    $start = max($current - 2, 1);
                    $end = min($start + 4, $last);
                    
                    // Adjust start if we're near the end
                    if ($end - $start < 4) {
                        $start = max($end - 4, 1);
                    }
                @endphp

                <!-- First Page + Ellipsis -->
                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->url(1) }}{{ request('search') ? '&search=' . request('search') : '' }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                <!-- Page Numbers -->
                @for($page = $start; $page <= $end; $page++)
                    <li class="page-item {{ $page == $current ? 'active' : '' }}">
                        @if($page == $current)
                            <span class="page-link">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $kkPaginator->url($page) }}{{ request('search') ? '&search=' . request('search') : '' }}">{{ $page }}</a>
                        @endif
                    </li>
                @endfor

                <!-- Last Page + Ellipsis -->
                @if($end < $last)
                    @if($end < $last - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->url($last) }}{{ request('search') ? '&search=' . request('search') : '' }}">{{ $last }}</a>
                    </li>
                @endif

                <!-- Next Page -->
                @if($kkPaginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                    </li>
                @endif
            </ul>
            
            <!-- Page Info -->
            <div class="text-center text-muted small mt-2">
                Halaman {{ $current }} dari {{ $last }}
            </div>
        </nav>

        <div class="text-muted small">
            Total: {{ $kkPaginator->total() }} KK
        </div>
    </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="suratModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Buat Surat untuk <span id="namaWarga"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="idWarga">

        <div class="mb-3">
            <label class="form-label fw-bold">Kategori Surat</label>
            <select id="kategoriSurat" class="form-select">
                <option value="">-- Pilih Kategori --</option>
                <option value="surat_keterangan">Surat Keterangan</option>
                <option value="surat_pengantar">Surat Pengantar</option>
                <option value="surat_pernyataan">Surat Pernyataan</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Jenis Surat</label>
            <select id="jenisSurat" class="form-select" disabled>
                <option value="">-- Pilih Jenis Surat --</option>
            </select>
        </div>

        <div class="alert alert-info small">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Format Nomor Surat:</strong> <code>Kode/Nomor/Bulan/Tahun</code><br>
            Contoh: <code>301/1/X/2025</code>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button id="btnGenerate" class="btn btn-success" disabled>
            <i class="fas fa-download me-1"></i>Generate Surat
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
/* Compact Pagination */
.pagination {
    flex-wrap: nowrap;
    margin-bottom: 0;
}

.page-item .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    min-width: 32px;
    text-align: center;
    border-radius: 4px;
    margin: 0 1px;
}

.page-item.active .page-link {
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .page-item {
        margin: 1px;
    }
}

/* Hover Effects */
.page-link:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

/* Existing Styles */
.bg-pink {
    background-color: #e83e8c !important;
}

.card {
    border-left: 4px solid #0d6efd !important;
}

.table th {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}

.btn-primary.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
.bg-pink {
    background-color: #e83e8c !important;
}

.card {
    border-left: 4px solid #0d6efd !important;
}

.table th {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("✅ Surat page script ready");

    const modalEl = document.getElementById('suratModal');
    const modal = new bootstrap.Modal(modalEl);

    // 🔥 DATA TEMPLATE DENGAN KODE NOMOR SURAT
    const suratOptions = {
        'surat_keterangan': [
            {id: 1, nama: '301 - Surat Keterangan (Umum)'},
            {id: 2, nama: '302 - Surat Keterangan Tidak Mampu'},
            {id: 3, nama: '303 - Surat Keterangan Domisili Tempat Tinggal'},
            {id: 4, nama: '304 - Surat Keterangan Usaha'},
            {id: 5, nama: '305 - Surat Keterangan Domisili Usaha'}
        ],
        'surat_pengantar': [
            {id: 6, nama: '401 - Surat Pengantar (Umum)'},
            {id: 7, nama: '402 - Surat Pengantar Catatan Kepolisian'},
            {id: 8, nama: '403 - Surat Pengantar Ijin Keramaian'}
        ],
        'surat_pernyataan': [
            {id: 9, nama: '501 - Surat Pernyataan (Umum)'},
            {id: 10, nama: '502 - Surat Pernyataan Ahli Waris'},
            {id: 11, nama: '503 - Surat Pernyataan Domisili Tempat Tinggal'},
            {id: 12, nama: '504 - Surat Pernyataan Pemberian Kuasa'}
        ]
    };

    // Tombol buat surat
    document.querySelectorAll('.open-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            document.getElementById('namaWarga').textContent = nama;
            document.getElementById('idWarga').value = id;
            document.getElementById('kategoriSurat').value = '';
            document.getElementById('jenisSurat').innerHTML = '<option value="">-- Pilih Jenis Surat --</option>';
            document.getElementById('jenisSurat').disabled = true;
            document.getElementById('btnGenerate').disabled = true;

            modal.show();
        });
    });

    // Pilih kategori
    document.getElementById('kategoriSurat').addEventListener('change', function() {
        const kategori = this.value;
        const jenisSelect = document.getElementById('jenisSurat');
        jenisSelect.innerHTML = '<option value="">-- Pilih Jenis Surat --</option>';
        document.getElementById('btnGenerate').disabled = true;

        if (kategori && suratOptions[kategori]) {
            suratOptions[kategori].forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nama;
                jenisSelect.appendChild(option);
            });
            jenisSelect.disabled = false;
        } else {
            jenisSelect.disabled = true;
        }
    });

    // Pilih jenis surat
    document.getElementById('jenisSurat').addEventListener('change', function() {
        document.getElementById('btnGenerate').disabled = !this.value;
    });

    // Generate surat
    document.getElementById('btnGenerate').addEventListener('click', function() {
        const wargaId = document.getElementById('idWarga').value;
        const templateId = document.getElementById('jenisSurat').value;

        if (!templateId) {
            alert('Pilih jenis surat terlebih dahulu!');
            return;
        }

        const generateUrl = `/surat/generate/${wargaId}/${templateId}`;
        
        console.log('Generate URL:', generateUrl);
        
        // Redirect ke URL generate
        window.location.href = generateUrl;
    });
});
</script>
@endsection