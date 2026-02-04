<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        $kkPerPage = 10; // Jumlah KK per halaman
        
        // Ambil semua data KK unik dengan jumlah anggotanya
        $kkList = Warga::select('no_kk')
            ->whereNotNull('no_kk')
            ->groupBy('no_kk')
            ->withCount(['anggota' => function($query) {
                $query->where('status_hidup', 'Hidup');
            }])
            ->orderBy('no_kk', 'asc')
            ->get();

        // Jika ada pencarian, filter KK list
       if ($request->filled('search')) {
            $keyword = $request->search;
            $kkList = $kkList->filter(function($kk) use ($keyword) {
                // Parse RT dan RW langsung di sini
                $lowerKeyword = strtolower(trim($keyword));
                $rtValue = null;
                $rwValue = null;
                
                // Cek pattern RT dan RW bersamaan - PERBAIKAN URUTAN
                if (preg_match('/rt\s*(\d+)\s*rw\s*(\d+)/i', $lowerKeyword, $matches)) {
                    // Format: RT X RW Y
                    $rtValue = (int)$matches[1];
                    $rwValue = (int)$matches[2];
                } elseif (preg_match('/rw\s*(\d+)\s*rt\s*(\d+)/i', $lowerKeyword, $matches)) {
                    // Format: RW Y RT X
                    $rwValue = (int)$matches[1];
                    $rtValue = (int)$matches[2];
                } elseif (preg_match('/rt\s*(\d+)/i', $lowerKeyword, $matches)) {
                    // Format: RT X
                    $rtValue = (int)$matches[1];
                } elseif (preg_match('/rw\s*(\d+)/i', $lowerKeyword, $matches)) {
                    // Format: RW X
                    $rwValue = (int)$matches[1];
                } elseif (preg_match('/\brt(\d+)\b/i', $lowerKeyword, $matches)) {
                    // Format: RTX
                    $rtValue = (int)$matches[1];
                } elseif (preg_match('/\brw(\d+)\b/i', $lowerKeyword, $matches)) {
                    // Format: RWX
                    $rwValue = (int)$matches[1];
                }
                
                // Cari KK yang memiliki anggota dengan keyword yang dicari
                $wargaInKK = Warga::where('no_kk', $kk->no_kk)
                    ->where(function($query) use ($keyword, $rtValue, $rwValue) {
                        // Jika ADA RT dan RW yang terdeteksi, gunakan KOMBINASI
                        if ($rtValue !== null && $rwValue !== null) {
                            $query->where('rt', $rtValue)
                                ->where('rw', $rwValue);
                        } 
                        // Jika HANYA RT yang terdeteksi
                        elseif ($rtValue !== null) {
                            $query->where('rt', $rtValue);
                        }
                        // Jika HANYA RW yang terdeteksi
                        elseif ($rwValue !== null) {
                            $query->where('rw', $rwValue);
                        }
                        // Jika TIDAK ADA RT/RW terdeteksi, gunakan pencarian biasa
                        else {
                            $query->where('nama', 'like', "%{$keyword}%")
                                ->orWhere('nik', 'like', "%{$keyword}%")
                                ->orWhere('no_kk', 'like', "%{$keyword}%")
                                ->orWhere('alamat', 'like', "%{$keyword}%");
                        }
                    })
                    ->exists();
                return $wargaInKK;
            });
        }

        // Pagination manual untuk KK
        $currentPage = Paginator::resolveCurrentPage('page');
        $currentPageKK = $kkList->slice(($currentPage - 1) * $kkPerPage, $kkPerPage);
        
        // Ambil data lengkap untuk KK yang akan ditampilkan
        $kkNumbers = $currentPageKK->pluck('no_kk')->toArray();
        
        $wargaData = Warga::whereIn('no_kk', $kkNumbers)
            ->orderBy('no_kk', 'asc')
            ->orderByRaw("CASE 
                WHEN status_nikah = 'Kawin' THEN 1 
                WHEN status_nikah = 'Belum Kawin' THEN 2 
                ELSE 3 
            END")
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('no_kk');

        // Buat paginator manual
        $kkPaginator = new LengthAwarePaginator(
            $wargaData,
            $kkList->count(),
            $kkPerPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        $canEdit = auth()->user()->role === 'admin';
        $totalKK = $kkList->count();
        
        return view('warga.index', compact('kkPaginator', 'canEdit', 'totalKK'));
    }

    public function create()
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Cek hak akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('warga.create');
    }

    public function store(Request $request)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Cek hak akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Validasi
        $validated = $request->validate([
            'nik' => 'required|unique:warga,nik|max:20',
            'no_kk' => 'nullable|string|max:20',
            'nama' => 'required|string|max:100',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date|before:today',
            'alamat' => 'nullable|string',
            'rt' => 'required|integer|min:1|max:100',
            'rw' => 'required|integer|min:1|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_nikah' => 'required|in:Belum Kawin,Kawin,Cerai,Cerai Mati',
            'status_hubungan_dalam_keluarga' => 'required|in:Kepala Keluarga,Suami,Istri,Anak,Menantu,Cucu,Orang Tua,Mertua,Famili Lain,Lainnya',
            'status_hidup' => 'required|in:Hidup,Meninggal',
            'agama' => 'required|in:Islam,Kristen Protestan,Katolik,Hindu,Buddha,Konghucu',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|in:Tidak Sekolah,SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3',
            'kewarganegaraan' => 'required|string|max:255',
        ], [
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nik.min' => 'NIK harus minimal 16 digit.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh lebih dari hari ini.',
            'nama_ayah.required' => 'Nama ayah wajib diisi.',
            'nama_ibu.required' => 'Nama ibu wajib diisi.',
            'status_hubungan_dalam_keluarga.required' => 'Status hubungan dalam keluarga wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
        ]);

        // Simpan data
        Warga::create($validated);

        return redirect()->route('warga.index')
                        ->with('success', 'Data warga berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Cek hak akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $warga = Warga::findOrFail($id);
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, $id)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Cek hak akses
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $warga = Warga::findOrFail($id);

        // Validasi
        $validated = $request->validate([
            'nik' => 'required|max:20|unique:warga,nik,' . $id,
            'no_kk' => 'nullable|string|max:20',
            'nama' => 'required|string|max:100',
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date|before:today',
            'alamat' => 'nullable|string',
            'rt' => 'required|integer|min:1|max:100',
            'rw' => 'required|integer|min:1|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_nikah' => 'required|in:Belum Kawin,Kawin,Cerai,Cerai Mati',
            'status_hubungan_dalam_keluarga' => 'required|in:Kepala Keluarga,Suami,Istri,Anak,Menantu,Cucu,Orang Tua,Mertua,Famili Lain,Lainnya',
            'status_hidup' => 'required|in:Hidup,Meninggal',
            'agama' => 'required|in:Islam,Kristen Protestan,Katolik,Hindu,Buddha,Konghucu',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|in:Tidak Sekolah,SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3',
            'kewarganegaraan' => 'required|string|max:255',
        ], [
            'nik.min' => 'NIK harus minimal 16 digit.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh lebih dari hari ini.',
            'nama_ayah.required' => 'Nama ayah wajib diisi.',
            'nama_ibu.required' => 'Nama ibu wajib diisi.',
            'status_hubungan_dalam_keluarga.required' => 'Status hubungan dalam keluarga wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
        ]);
        // Update data
        $warga->update($validated);

        return redirect()->route('warga.index')
                        ->with('success', 'Data warga berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak valid.'
            ], 401);
        }

        try {
            DB::beginTransaction();

            // ✅ Cek hak akses
            if (auth()->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            $warga = Warga::findOrFail($id);
            
            // ✅ Security: Prevent deletion of important records
            if ($this->isProtectedRecord($warga)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data ini tidak dapat dihapus karena termasuk dalam data penting sistem.'
                ], 422);
            }

            // ✅ Check if warga has related data in other tables
            if ($this->hasRelatedRecords($warga)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak dapat dihapus karena memiliki relasi dengan data surat/riwayat.'
                ], 422);
            }

            // ✅ Log deletion activity
            Log::info('Warga deleted', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'warga_id' => $warga->id,
                'warga_nik' => $warga->nik,
                'warga_nama' => $warga->nama,
                'deleted_at' => now()
            ]);

            $nama = $warga->nama;
            $warga->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Data warga {$nama} berhasil dihapus!",
                'data' => ['id' => $id]
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error during warga deletion: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: Terjadi kesalahan database.'
            ], 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting warga: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if warga has related records in other tables
     */
    private function hasRelatedRecords($warga)
    {
        try {
            // Cek di tabel riwayat_surat jika ada
            if (Schema::hasTable('riwayat_surat')) {
                $hasRiwayat = DB::table('riwayat_surat')
                    ->where('warga_id', $warga->id)
                    ->exists();
                if ($hasRiwayat) return true;
            }

            // Cek di tabel templates jika ada relasi
            if (Schema::hasTable('templates')) {
                // Sesuaikan dengan struktur relasi Anda
            }

            // Tambahkan pengecekan tabel lain sesuai kebutuhan

            return false;
            
        } catch (\Exception $e) {
            Log::error('Error checking related records: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if record is protected (important system records)
     */
    private function isProtectedRecord($warga)
    {
        // Contoh: Cegah penghapusan data dengan NIK tertentu
        $protectedNiks = [
            // '3374010101010001', // Contoh NIK yang diproteksi
        ];

        // Cegah penghapusan jika warga adalah kepala keluarga dan masih ada anggota
        if ($warga->no_kk) {
            $activeFamilyMembers = Warga::where('no_kk', $warga->no_kk)
                ->where('id', '!=', $warga->id)
                ->where('status_hidup', 'Hidup')
                ->count();
            
            if ($activeFamilyMembers > 0) {
                return true;
            }
        }

        return in_array($warga->nik, $protectedNiks);
    }

    /**
     * Live search untuk warga
     */
    public function searchAjax(Request $request)
    {
        // 🔥 CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return response()->json(['error' => 'Session tidak valid'], 401);
        }

        $keyword = $request->get('q');
        
        $warga = Warga::where('nama', 'like', "%{$keyword}%")
            ->orWhere('nik', 'like', "%{$keyword}%")
            ->orWhere('no_kk', 'like', "%{$keyword}%")
            ->select('id', 'nik', 'nama', 'no_kk')
            ->limit(10)
            ->get();

        return response()->json($warga);
    }
}