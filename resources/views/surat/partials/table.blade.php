@php
    // Group warga by no_kk
    $groupedWarga = $warga->groupBy('no_kk');
    $counter = ($warga->currentPage() - 1) * $warga->perPage() + 1;
@endphp

@forelse($groupedWarga as $no_kk => $keluarga)
    <!-- Kartu Keluarga -->
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-home me-2"></i>
                        Kartu Keluarga: <strong>{{ $no_kk ?? 'TIDAK ADA KK' }}</strong>
                    </h5>
                    <small class="opacity-75">
                        {{ $keluarga->first()->alamat }} - 
                        RT {{ $keluarga->first()->rt }}/RW {{ $keluarga->first()->rw }}
                    </small>
                </div>
                <span class="badge bg-light text-dark fs-6">
                    {{ $keluarga->count() }} Anggota
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
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
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
@empty
    <!-- Empty State -->
    <div class="text-center mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">
                    @if(request('search'))
                    Tidak ada warga ditemukan
                    @else
                    Belum ada data warga
                    @endif
                </h5>
                <p class="text-muted mb-3">
                    @if(request('search'))
                    Tidak ditemukan warga untuk pencarian: "{{ request('search') }}"
                    @else
                    Data warga akan ditampilkan di sini
                    @endif
                </p>
            </div>
        </div>
    </div>
@endforelse