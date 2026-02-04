@php
    $groupedWarga = $warga->groupBy('no_kk');
@endphp

@forelse($groupedWarga as $no_kk => $keluarga)
<div class="card mb-4 family-card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0">
                    <i class="fas fa-home me-2"></i>
                    Keluarga: <strong>{{ $no_kk ?? 'TANPA KK' }}</strong>
                </h6>
                <small>{{ $keluarga->first()->alamat }} - RT {{ $keluarga->first()->rt }}/RW {{ $keluarga->first()->rw }}</small>
            </div>
            <span class="badge bg-light text-dark">{{ $keluarga->count() }} Anggota</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @foreach($keluarga as $item)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                {{ $item->nama }}
                                @if($loop->first)
                                <span class="badge bg-success ms-1">Kepala Keluarga</span>
                                @endif
                                @if($item->status_hidup == 'Meninggal')
                                <span class="badge bg-danger ms-1">Meninggal</span>
                                @endif
                            </h6>
                            <p class="mb-1 text-muted">
                                NIK: {{ $item->nik }} | 
                                {{ $item->jenis_kelamin }} | 
                                {{ $item->status_nikah }} | 
                                {{ $item->pekerjaan ?? 'Tidak bekerja' }}
                            </p>
                        </div>
                        @if($canEdit)
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('warga.edit', $item->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger delete-btn" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->nama }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <i class="fas fa-users fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Belum ada data warga</h5>
</div>
@endforelse