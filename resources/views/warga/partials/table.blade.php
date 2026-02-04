@php
    // Group warga by no_kk
    $groupedWarga = $warga->groupBy('no_kk');
    $counter = ($warga->currentPage() - 1) * $warga->perPage() + 1;
@endphp

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
                @if($canEdit)
                <th width="100" class="text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($groupedWarga as $no_kk => $keluarga)
                <!-- Header Keluarga -->
                <tr class="table-primary family-header">
                    <td colspan="{{ $canEdit ? 8 : 7 }}" class="bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">
                                    <i class="fas fa-home me-2"></i>Keluarga: {{ $no_kk ?? 'TIDAK ADA KK' }}
                                </strong>
                                <span class="badge bg-secondary ms-2">{{ $keluarga->count() }} Anggota</span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-chevron-down"></i>
                            </small>
                        </div>
                    </td>
                </tr>

                <!-- Anggota Keluarga -->
                @foreach($keluarga as $index => $item)
                    @php
                        // ✅ GUNAKAN DATA DARI DATABASE - lebih akurat
                        $hubungan = $item->status_hubungan_dalam_keluarga ?? 'Anak';
                        
                        // Tentukan class badge berdasarkan hubungan
                        $badgeClass = 'bg-info'; // default
                        if ($hubungan == 'Kepala Keluarga') $badgeClass = 'bg-success';
                        elseif ($hubungan == 'Suami' || $hubungan == 'Istri') $badgeClass = 'bg-primary';
                        elseif ($hubungan == 'Orang Tua') $badgeClass = 'bg-warning text-dark';
                        elseif ($hubungan == 'Mertua') $badgeClass = 'bg-secondary';
                        
                        // Tentukan apakah kepala keluarga untuk icon mahkota
                        $isKepalaKeluarga = $hubungan == 'Kepala Keluarga';
                    @endphp

                    <tr class="@if($item->status_hidup == 'Meninggal') table-danger @endif">
                        <td class="text-center fw-bold">{{ $counter++ }}</td>
                        <td>
                            <code>{{ $item->nik }}</code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <strong class="{{ $isKepalaKeluarga ? 'text-primary' : '' }}">
                                    {{ $item->nama }}
                                    @if($isKepalaKeluarga)
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
                            <span class="badge {{ $badgeClass }}">
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
                        @if($canEdit)
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('warga.edit', $item->id) }}" 
                                   class="btn btn-warning" 
                                   title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-danger" 
                                        onclick="showDeleteModal(
                                            {{ $item->id }}, 
                                            '{{ addslashes($item->nama) }}', 
                                            '{{ $item->nik }}', 
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

                    <!-- Separator antara anggota dalam keluarga yang sama -->
                    @if(!$loop->last)
                    <tr class="family-separator">
                        <td colspan="{{ $canEdit ? 8 : 7 }}" class="bg-light" style="height: 3px;"></td>
                    </tr>
                    @endif
                @endforeach

                <!-- Separator antara keluarga yang berbeda -->
                @if(!$loop->last)
                <tr class="family-group-separator">
                    <td colspan="{{ $canEdit ? 8 : 7 }}" class="border-bottom" style="height: 20px; background-color: #f8f9fa;"></td>
                </tr>
                @endif

            @empty
            <tr>
                <td colspan="{{ $canEdit ? 8 : 7 }}" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                        <h5 class="text-muted mb-2">Belum ada data warga</h5>
                        <p class="text-muted mb-3">Data warga akan ditampilkan di sini</p>
                        @if($canEdit)
                        <a href="{{ route('warga.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Tambah Warga Pertama
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<style>
/* Force badge styles */
.badge.bg-pink {
    background-color: #e83e8c !important;
    color: white !important;
    border: 1px solid #e83e8c !important;
}

/* Atau gunakan attribute selector */
span.badge[style*="bg-pink"] {
    background-color: #e83e8c !important;
    color: white !important;
}

/* Style untuk header keluarga */
.family-header td {
    border-left: 4px solid #0d6efd !important;
}

/* Style untuk separator */
.family-separator td {
    padding: 0 !important;
    background-color: #f8f9fa !important;
}

.family-group-separator td {
    padding: 0 !important;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 5px,
        #e9ecef 5px,
        #e9ecef 10px
    ) !important;
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

/* Status meninggal */
.table-danger {
    opacity: 0.7;
}

.table-danger:hover {
    opacity: 0.9;
}
</style>