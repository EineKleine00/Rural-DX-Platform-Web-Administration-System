<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Warga - Sistem Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 1rem;
        }
        .required-label::after {
            content: " *";
            color: red;
        }
        .section-title {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            color: #0d6efd;
        }
        .invalid-feedback {
            display: block;
        }
        .form-header {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0;
            margin: -2rem -2rem 2rem -2rem;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4 mb-4">
    <div class="form-container">
        <div class="form-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-person-plus me-2"></i>Tambah Data Warga
                    </h2>
                    <p class="mb-0 opacity-75">Isi form berikut untuk menambahkan data warga baru</p>
                </div>
                <a href="{{ route('warga.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <h6><i class="bi bi-exclamation-triangle me-2"></i>Terjadi Kesalahan:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('warga.store') }}" method="POST" id="addWargaForm">
            @csrf

            <!-- Data Pribadi -->
            <div class="row">
                <div class="col-12">
                    <h5 class="section-title">
                        <i class="bi bi-person-vcard me-2"></i>Data Pribadi
                    </h5>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">NIK</label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" 
                           value="{{ old('nik') }}" required maxlength="16" 
                           placeholder="Masukkan 16 digit NIK">
                    <div class="form-text">Nomor Induk Kependudukan 16 digit</div>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Kartu Keluarga</label>
                    <input type="text" name="no_kk" class="form-control @error('no_kk') is-invalid @enderror" 
                           value="{{ old('no_kk') }}" maxlength="16"
                           placeholder="Nomor Kartu Keluarga">
                    <div class="form-text">Opsional, isi jika tersedia</div>
                    @error('no_kk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label required-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                           value="{{ old('nama') }}" required 
                           placeholder="Masukkan nama lengkap sesuai KTP">
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Nama Ayah</label>
                    <input type="text" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror" 
                           value="{{ old('nama_ayah') }}" required
                           placeholder="Nama ayah kandung">
                    @error('nama_ayah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Nama Ibu</label>
                    <input type="text" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror" 
                           value="{{ old('nama_ibu') }}" required
                           placeholder="Nama ibu kandung">
                    @error('nama_ibu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" 
                           value="{{ old('tempat_lahir') }}" 
                           placeholder="Kota tempat lahir">
                    @error('tempat_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                           value="{{ old('tanggal_lahir') }}"
                           max="{{ date('Y-m-d') }}">
                    @error('tanggal_lahir')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Data Alamat -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="section-title">
                        <i class="bi bi-house-door me-2"></i>Data Alamat
                    </h5>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" 
                              placeholder="Alamat lengkap tempat tinggal">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required-label">RT</label>
                    <input type="number" name="rt" class="form-control @error('rt') is-invalid @enderror" 
                           value="{{ old('rt') }}" required min="1" max="100"
                           placeholder="Contoh: 1">
                    @error('rt')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required-label">RW</label>
                    <input type="number" name="rw" class="form-control @error('rw') is-invalid @enderror" 
                           value="{{ old('rw') }}" required min="1" max="100"
                           placeholder="Contoh: 1">
                    @error('rw')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label required-label">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" class="form-control @error('kewarganegaraan') is-invalid @enderror" 
                           value="{{ old('kewarganegaraan', 'Indonesia') }}" required
                           placeholder="Kewarganegaraan">
                    @error('kewarganegaraan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Data Status & Pekerjaan -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2"></i>Data Status & Pekerjaan
                    </h5>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Status Perkawinan</label>
                    <select name="status_nikah" class="form-select @error('status_nikah') is-invalid @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="Belum Kawin" {{ old('status_nikah') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                        <option value="Kawin" {{ old('status_nikah') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                        <option value="Cerai" {{ old('status_nikah') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                        <option value="Cerai Mati" {{ old('status_nikah') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                    </select>
                    @error('status_nikah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Status Hidup</label>
                    <select name="status_hidup" class="form-select @error('status_hidup') is-invalid @enderror" required>
                        <option value="">Pilih Status</option>
                        <option value="Hidup" {{ old('status_hidup', 'Hidup') == 'Hidup' ? 'selected' : '' }}>Hidup</option>
                        <option value="Meninggal" {{ old('status_hidup') == 'Meninggal' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                    @error('status_hidup')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Agama</label>
                    <select name="agama" class="form-select @error('agama') is-invalid @enderror" required>
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen Protestan" {{ old('agama') == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                        <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                    @error('agama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label required-label">Pendidikan</label>
                    <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror" required>
                        <option value="">Pilih Pendidikan</option>
                        <option value="Tidak Sekolah" {{ old('pendidikan') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                        <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                        <option value="D1" {{ old('pendidikan') == 'D1' ? 'selected' : '' }}>D1</option>
                        <option value="D2" {{ old('pendidikan') == 'D2' ? 'selected' : '' }}>D2</option>
                        <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                        <option value="D4" {{ old('pendidikan') == 'D4' ? 'selected' : '' }}>D4</option>
                        <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ old('pendidikan') == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                    @error('pendidikan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label required-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" 
                           value="{{ old('pekerjaan') }}" required
                           placeholder="Contoh: Pedagang, PNS, Wiraswasta, Pelajar, dll.">
                    @error('pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-end border-top pt-3">
                        <a href="{{ route('warga.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </a>
                        <button type="reset" class="btn btn-outline-danger">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Warga
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Validasi client-side
    document.getElementById('addWargaForm').addEventListener('submit', function(e) {
        const nik = document.querySelector('input[name="nik"]').value;
        const nama = document.querySelector('input[name="nama"]').value;
        const namaAyah = document.querySelector('input[name="nama_ayah"]').value;
        const namaIbu = document.querySelector('input[name="nama_ibu"]').value;
        const rt = document.querySelector('input[name="rt"]').value;
        const rw = document.querySelector('input[name="rw"]').value;
        const agama = document.querySelector('select[name="agama"]').value;
        const pekerjaan = document.querySelector('input[name="pekerjaan"]').value;
        const pendidikan = document.querySelector('select[name="pendidikan"]').value;
        const kewarganegaraan = document.querySelector('input[name="kewarganegaraan"]').value;
        const jenisKelamin = document.querySelector('select[name="jenis_kelamin"]').value;
        const statusNikah = document.querySelector('select[name="status_nikah"]').value;
        const statusHidup = document.querySelector('select[name="status_hidup"]').value;
        const statusHubungan = document.querySelector('select[name="status_hubungan_dalam_keluarga"]').value;
        
        // Validasi required fields
        const requiredFields = [
            { field: nik, name: 'NIK' },
            { field: nama, name: 'Nama Lengkap' },
            { field: namaAyah, name: 'Nama Ayah' },
            { field: namaIbu, name: 'Nama Ibu' },
            { field: rt, name: 'RT' },
            { field: rw, name: 'RW' },
            { field: agama, name: 'Agama' },
            { field: pekerjaan, name: 'Pekerjaan' },
            { field: pendidikan, name: 'Pendidikan' },
            { field: kewarganegaraan, name: 'Kewarganegaraan' },
            { field: jenisKelamin, name: 'Jenis Kelamin' },
            { field: statusNikah, name: 'Status Perkawinan' },
            { field: statusHidup, name: 'Status Hidup' },
            { field: statusHubungan, name: 'Status Hubungan dalam Keluarga' }
        ];

        for (let item of requiredFields) {
            if (!item.field) {
                e.preventDefault();
                alert(`Field "${item.name}" wajib diisi!`);
                return false;
            }
        }
        
        // Validasi NIK 16 digit
        if (nik.length !== 16) {
            e.preventDefault();
            alert('NIK harus 16 digit!');
            return false;
        }

        // Validasi No. KK jika diisi harus 16 digit
        const noKK = document.querySelector('input[name="no_kk"]').value;
        if (noKK && noKK.length !== 16) {
            e.preventDefault();
            alert('No. KK harus 16 digit jika diisi!');
            return false;
        }
    });

    // Auto-format untuk input number
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < parseInt(this.min)) {
                this.value = this.min;
            }
            if (this.value > parseInt(this.max)) {
                this.value = this.max;
            }
        });
    });

    // Real-time validation untuk NIK dan No KK (hanya angka)
    document.querySelector('input[name="nik"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 16) {
            this.value = this.value.slice(0, 16);
        }
    });

    document.querySelector('input[name="no_kk"]').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 16) {
            this.value = this.value.slice(0, 16);
        }
    });

    // Auto-focus ke field pertama
    document.querySelector('input[name="nik"]').focus();
</script>
</body>
</html>