@extends('layouts.app')

@section('title', 'Data Warga per Kartu Keluarga')

@section('head')
<script>
// ✅ FUNGSI GLOBAL UNTUK DELETE MODAL - VERSI SIMPLE
function showDeleteModal(id, name, nik, noKk, hubungan) {
    console.log('🎯 Membuka modal delete dengan data:', { id, name, nik, noKk, hubungan });
    
    // Validasi data
    if (!id || !name) {
        console.error('❌ Data tidak lengkap');
        showAlert('danger', 'Terjadi kesalahan: Data tidak lengkap');
        return;
    }

    // Update modal content - langsung dari parameter
    const deleteNama = document.getElementById('deleteNama');
    const deleteNik = document.getElementById('deleteNik');
    const deleteKk = document.getElementById('deleteKk');
    const deleteHubungan = document.getElementById('deleteHubungan');
    
    if (deleteNama) deleteNama.textContent = name || 'Tidak ada nama';
    if (deleteNik) deleteNik.textContent = nik || 'Tidak ada NIK';
    if (deleteKk) deleteKk.textContent = noKk || 'Tidak ada KK';
    if (deleteHubungan) deleteHubungan.textContent = hubungan || 'Tidak diketahui';
    
    // Update form action
    const form = document.getElementById('deleteForm');
    if (form) {
        form.action = `/warga/${id}`;
        console.log('📝 Form action diupdate ke:', form.action);
    } else {
        console.error('❌ Form delete tidak ditemukan');
        showAlert('danger', 'Terjadi kesalahan: Form tidak ditemukan');
        return;
    }
    
    // Reset security check
    const securityConfirm = document.getElementById('securityConfirm');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (securityConfirm) securityConfirm.value = '';
    if (confirmDeleteBtn) confirmDeleteBtn.disabled = true;
    
    // Show modal
    const deleteModalElement = document.getElementById('deleteModal');
    if (deleteModalElement) {
        const deleteModal = new bootstrap.Modal(deleteModalElement);
        deleteModal.show();
        console.log('✅ Modal berhasil ditampilkan');
    } else {
        console.error('❌ Element modal tidak ditemukan');
        showAlert('danger', 'Terjadi kesalahan: Modal tidak ditemukan');
    }
}

// ✅ ALERT FUNCTION
function showAlert(type, message, isSuccess = false) {
    // Remove existing alerts
    document.querySelectorAll('.custom-alert').forEach(alert => {
        if (alert.parentNode) alert.remove();
    });
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    
    const icon = isSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="${icon} me-2 fs-5"></i>
            <div class="flex-grow-1">
                <strong>${isSuccess ? 'Berhasil' : 'Peringatan'}:</strong> ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds for success, 8 seconds for errors
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, isSuccess ? 5000 : 8000);
}
</script>
@endsection

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-primary mb-1">
                <i class="fas fa-home me-2"></i>Data Kartu Keluarga
            </h4>
            <p class="text-muted mb-0">Setiap KK ditampilkan utuh tanpa terpisah</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('warga.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Warga
        </a>
        @endif
    </div>

    <!-- Search Form -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('warga.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari KK atau Anggota Keluarga:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               id="search"
                               value="{{ request('search') }}"
                               class="form-control border-start-0" 
                               placeholder="Cari berdasarkan no KK, nama, NIK...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
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

    <!-- KK List -->
    @foreach($kkPaginator as $no_kk => $anggotaKeluarga)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-home me-2"></i>
                        Kartu Keluarga: <strong>{{ $no_kk }}</strong>
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
                            @if(auth()->user()->role === 'admin')
                            <th width="100" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anggotaKeluarga as $index => $warga)
                        @php
                            $hubungan = $warga->status_hubungan_dalam_keluarga;
                        @endphp
                        <tr class="@if($warga->status_hidup == 'Meninggal') table-danger @endif">
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <code>{{ $warga->nik }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <strong class="{{ $hubungan === 'Kepala Keluarga' ? 'text-primary' : '' }}">
                                        {{ $warga->nama }}
                                        @if($hubungan === 'Kepala Keluarga')
                                            <i class="fas fa-crown text-warning ms-1" title="Kepala Keluarga"></i>
                                        @endif
                                    </strong>
                                    @if($warga->status_hidup == 'Meninggal')
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
                                <span class="badge {{ $warga->jenis_kelamin == 'Laki-laki' ? 'bg-primary' : 'bg-pink' }}">
                                    {{ $warga->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $warga->pekerjaan ?? 'Tidak bekerja' }}</small>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($warga->status_nikah == 'Kawin') bg-success
                                    @elseif($warga->status_nikah == 'Belum Kawin') bg-info
                                    @elseif($warga->status_nikah == 'Cerai') bg-warning
                                    @else bg-secondary @endif">
                                    {{ $warga->status_nikah }}
                                </span>
                            </td>
                            @if(auth()->user()->role === 'admin')
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('warga.edit', $warga->id) }}" 
                                       class="btn btn-warning" 
                                       title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="showDeleteModal(
                                                {{ $warga->id }}, 
                                                '{{ addslashes($warga->nama) }}', 
                                                '{{ $warga->nik }}', 
                                                '{{ $no_kk }}', 
                                                '{{ $hubungan }}'
                                            )"
                                            title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    @if($kkPaginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted small">
            {{ $kkPaginator->count() }} KK per halaman
        </div>
        
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <!-- Previous Page Link -->
                @if($kkPaginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left me-1"></i> Prev
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}" rel="prev">
                            <i class="fas fa-chevron-left me-1"></i> Prev
                        </a>
                    </li>
                @endif

                <!-- First Page -->
                @if($kkPaginator->currentPage() > 3)
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->url(1) }}{{ request('search') ? '&search=' . request('search') : '' }}">1</a>
                    </li>
                    @if($kkPaginator->currentPage() > 4)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                <!-- Pagination Elements - Limited to 5 pages -->
                @php
                    $start = max($kkPaginator->currentPage() - 2, 1);
                    $end = min($start + 4, $kkPaginator->lastPage());
                    $start = max($end - 4, 1); // Adjust start if near the end
                @endphp

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $kkPaginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $kkPaginator->url($page) }}{{ request('search') ? '&search=' . request('search') : '' }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                <!-- Last Page -->
                @if($kkPaginator->currentPage() < $kkPaginator->lastPage() - 2)
                    @if($kkPaginator->currentPage() < $kkPaginator->lastPage() - 3)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->url($kkPaginator->lastPage()) }}{{ request('search') ? '&search=' . request('search') : '' }}">{{ $kkPaginator->lastPage() }}</a>
                    </li>
                @endif

                <!-- Next Page Link -->
                @if($kkPaginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $kkPaginator->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}" rel="next">
                            Next <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            Next <i class="fas fa-chevron-right ms-1"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>

        <div class="text-muted small">
            Total: {{ $totalKK }} KK
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if($kkPaginator->count() == 0)
    <div class="text-center mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-5">
                <i class="fas fa-home fa-4x text-muted mb-3"></i>
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
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('warga.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah KK Pertama
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
// ✅ FUNGSI GLOBAL UNTUK DELETE MODAL - VERSI SUPER SIMPLE
function showDeleteModal(id, name, nik, noKk, hubungan) {
    console.log('🎯 Membuka modal delete dengan data:', { id, name, nik, noKk, hubungan });
    
    // ✅ PASTIKAN DATA LANGSUNG TERISI
    document.getElementById('deleteNama').textContent = name || 'Tidak ada nama';
    document.getElementById('deleteNik').textContent = nik || 'Tidak ada NIK';
    document.getElementById('deleteKk').textContent = noKk || 'Tidak ada KK';
    document.getElementById('deleteHubungan').textContent = hubungan || 'Tidak diketahui';
    
    // Update form action
    const form = document.getElementById('deleteForm');
    if (form) {
        form.action = `/warga/${id}`;
        console.log('📝 Form action diupdate ke:', form.action);
    }
    
    // Show modal
    const deleteModalElement = document.getElementById('deleteModal');
    if (deleteModalElement) {
        const deleteModal = new bootstrap.Modal(deleteModalElement);
        deleteModal.show();
        console.log('✅ Modal berhasil ditampilkan');
    }
}

// ✅ AJAX DELETE HANDLER - SIMPLE
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Delete system initialized - Simple Version');
    
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('confirmDeleteBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menghapus...';
            
            console.log('🔄 Mengirim request delete ke:', form.action);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Delete response:', data);
                
                if (data.success) {
                    // Show success message
                    alert('✅ ' + data.message);
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) modal.hide();
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } else {
                    alert('❌ ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('❌ Delete error:', error);
                alert('❌ Terjadi kesalahan saat menghapus data');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
// Pagination Optimization
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Optimizing pagination...');
    
    // Limit visible page links
    const pageLinks = document.querySelectorAll('.page-item:not(.disabled):not(.active)');
    const currentPage = {{ $kkPaginator->currentPage() }};
    const lastPage = {{ $kkPaginator->lastPage() }};
    
    if (lastPage > 7) {
        console.log(`📄 Pagination optimized: ${currentPage} of ${lastPage}`);
        
        // You can add dynamic behavior here if needed
        // For example, show first, last, and pages around current
    }
});

// ✅ TEST FUNCTION: Cek apakah modal berfungsi
function testDeleteModal() {
    console.log('🧪 Testing delete modal...');
    showDeleteModal(
        999, 
        'Nama Test', 
        '1234567890123456', 
        '9876543210987654', 
        'Kepala Keluarga'
    );
}

// Panggil test function (bisa dihapus setelah testing)
// testDeleteModal();
</script>

<style>
/* Pagination Styling */
.pagination {
    flex-wrap: nowrap;
    max-width: 100%;
    overflow: hidden;
}

.page-item {
    margin: 0 1px;
}

.page-link {
    border-radius: 4px !important;
    min-width: 38px;
    text-align: center;
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .page-item {
        margin: 1px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        text-align: center;
    }
}

/* Hover effects */
.page-link:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.page-item.active .page-link {
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Custom colors */
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

.btn-danger:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
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

.btn-danger:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
</style>
@endsection

<!-- ✅ DELETE MODAL -->
<!-- ✅ DELETE MODAL SIMPLE -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Data yang akan dihapus -->
                <div class="mb-3">
                    <p class="mb-2 fw-bold">Anda akan menghapus data berikut:</p>
                    <div class="card border">
                        <div class="card-body py-2">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="30%" class="fw-bold text-muted">Nama:</td>
                                    <td id="deleteNama" class="fw-bold">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">NIK:</td>
                                    <td id="deleteNik" class="font-monospace">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">No. KK:</td>
                                    <td id="deleteKk" class="font-monospace">-</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Hubungan:</td>
                                    <td id="deleteHubungan">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Simple Confirmation -->
                <div class="alert alert-warning border-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="confirmDeleteBtn" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>