{{-- resources/views/warga/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Data Warga')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Warga</h5>
                    <a href="{{ route('warga.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('warga.update', $warga->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Data Pribadi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                                   id="nik" name="nik" value="{{ old('nik', $warga->nik) }}" 
                                                   required maxlength="20">
                                            @error('nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="no_kk" class="form-label">No. KK</label>
                                            <input type="text" class="form-control @error('no_kk') is-invalid @enderror" 
                                                   id="no_kk" name="no_kk" value="{{ old('no_kk', $warga->no_kk) }}" 
                                                   maxlength="20">
                                            @error('no_kk')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                                   id="nama" name="nama" value="{{ old('nama', $warga->nama) }}" 
                                                   required maxlength="100">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="nama_ayah" class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" 
                                                           id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah', $warga->nama_ayah) }}" 
                                                           required maxlength="255">
                                                    @error('nama_ayah')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="nama_ibu" class="form-label">Nama Ibu <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('nama_ibu') is-invalid @enderror" 
                                                           id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu', $warga->nama_ibu) }}" 
                                                           required maxlength="255">
                                                    @error('nama_ibu')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                                           id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $warga->tempat_lahir) }}" 
                                                           maxlength="100">
                                                    @error('tempat_lahir')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tanggal Lahir</label>
                                                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                                    value="{{ old('tanggal_lahir', $warga->tanggal_lahir ? \Carbon\Carbon::parse($warga->tanggal_lahir)->format('Y-m-d') : '') }}"
                                                    max="{{ date('Y-m-d') }}">
                                                @error('tanggal_lahir')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Data Alamat</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                                      id="alamat" name="alamat" rows="3">{{ old('alamat', $warga->alamat) }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="rt" class="form-label">RT <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('rt') is-invalid @enderror" 
                                                           id="rt" name="rt" value="{{ old('rt', $warga->rt) }}" 
                                                           required min="1" max="100">
                                                    @error('rt')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="rw" class="form-label">RW <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('rw') is-invalid @enderror" 
                                                           id="rw" name="rw" value="{{ old('rw', $warga->rw) }}" 
                                                           required min="1" max="100">
                                                    @error('rw')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Data Lainnya</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki" {{ old('jenis_kelamin', $warga->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ old('jenis_kelamin', $warga->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                    @error('jenis_kelamin')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status_nikah" class="form-label">Status Nikah <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status_nikah') is-invalid @enderror" 
                                                            id="status_nikah" name="status_nikah" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="Belum Kawin" {{ old('status_nikah', $warga->status_nikah) == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                                        <option value="Kawin" {{ old('status_nikah', $warga->status_nikah) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                                        <option value="Cerai" {{ old('status_nikah', $warga->status_nikah) == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                                        <option value="Cerai Mati" {{ old('status_nikah', $warga->status_nikah) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                                    </select>
                                                    @error('status_nikah')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                            <label class="form-label required-label">Status Hubungan dalam Keluarga</label>
                                            <select name="status_hubungan_dalam_keluarga" class="form-select @error('status_hubungan_dalam_keluarga') is-invalid @enderror" required>
                                                <option value="">Pilih Status Hubungan</option>
                                                <option value="Kepala Keluarga" {{ old('status_hubungan_dalam_keluarga') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                                <option value="Suami/Istri" {{ old('status_hubungan_dalam_keluarga') == 'Suami/Istri' ? 'selected' : '' }}>Suami/Istri</option>
                                                <option value="Anak" {{ old('status_hubungan_dalam_keluarga') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                                <option value="Menantu" {{ old('status_hubungan_dalam_keluarga') == 'Menantu' ? 'selected' : '' }}>Menantu</option>
                                                <option value="Cucu" {{ old('status_hubungan_dalam_keluarga') == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                                                <option value="Orang Tua" {{ old('status_hubungan_dalam_keluarga') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                                <option value="Mertua" {{ old('status_hubungan_dalam_keluarga') == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                                                <option value="Famili Lain" {{ old('status_hubungan_dalam_keluarga') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                                                <option value="Lainnya" {{ old('status_hubungan_dalam_keluarga') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            @error('status_hubungan_dalam_keluarga')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="agama" class="form-label">Agama <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('agama') is-invalid @enderror" 
                                                            id="agama" name="agama" required>
                                                        <option value="">Pilih Agama</option>
                                                        <option value="Islam" {{ old('agama', $warga->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                                        <option value="Kristen Protestan" {{ old('agama', $warga->agama) == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                                                        <option value="Katolik" {{ old('agama', $warga->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                                        <option value="Hindu" {{ old('agama', $warga->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                                        <option value="Buddha" {{ old('agama', $warga->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                                        <option value="Konghucu" {{ old('agama', $warga->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                                    </select>
                                                    @error('agama')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" 
                                                           id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $warga->pekerjaan) }}" 
                                                           required maxlength="255">
                                                    @error('pekerjaan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="pendidikan" class="form-label">Pendidikan <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('pendidikan') is-invalid @enderror" 
                                                           id="pendidikan" name="pendidikan" value="{{ old('pendidikan', $warga->pendidikan) }}" 
                                                           required maxlength="255">
                                                    @error('pendidikan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="kewarganegaraan" class="form-label">Kewarganegaraan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('kewarganegaraan') is-invalid @enderror" 
                                                   id="kewarganegaraan" name="kewarganegaraan" 
                                                   value="{{ old('kewarganegaraan', $warga->kewarganegaraan) }}" 
                                                   required maxlength="255">
                                            @error('kewarganegaraan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Data
                            </button>
                            <a href="{{ route('warga.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush