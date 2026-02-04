<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Template;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SuratController extends Controller
{
    /**
     * Display a listing of the warga for surat creation.
     */
    public function index(Request $request)
    {
        // CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        $query = Warga::query();

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('nik', 'like', "%{$keyword}%")
                  ->orWhere('nama', 'like', "%{$keyword}%")
                  ->orWhere('alamat', 'like', "%{$keyword}%")
                  ->orWhere('no_kk', 'like', "%{$keyword}%");
            });
        }

        // Ambil semua data warga
        $allWarga = $query->orderBy('no_kk', 'asc')
                         ->orderBy('id', 'asc')
                         ->get();

        // Group by no_kk
        $groupedWarga = $allWarga->groupBy('no_kk');

        // Pagination manual untuk KK
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // 10 KK per halaman
        $currentItems = $groupedWarga->slice(($currentPage - 1) * $perPage, $perPage);
        
        // Buat paginator
        $kkPaginator = new LengthAwarePaginator(
            $currentItems,
            $groupedWarga->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        // Tambahkan parameter search ke pagination links
        if ($request->has('search')) {
            $kkPaginator->appends(['search' => $request->search]);
        }

        // Get template options untuk JavaScript
        $templateOptions = Template::getTemplateOptionsForJS();

        return view('surat.index', compact('kkPaginator', 'templateOptions'));
    }

    /**
     * Generate surat based on template
     */
    public function generate($warga_id, $template_id)
    {
        // CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        try {
            \Log::info('Starting surat generation', ['warga_id' => $warga_id, 'template_id' => $template_id]);

            // ✅ Ambil data warga
            $warga = Warga::findOrFail($warga_id);
            \Log::info('Warga found', ['nama' => $warga->nama]);

            // ✅ Ambil template dari database
            $template = Template::findOrFail($template_id);
            \Log::info('Template found', ['nama_template' => $template->nama_template, 'kode_surat' => $template->nomor_surat]);

            // ✅ 🔥 GENERATE NOMOR SURAT OTOMATIS dengan counter per jenis surat
            $nomorSurat = $this->generateNomorSuratWithCounter($template);
            \Log::info('Nomor surat generated', ['nomor_surat' => $nomorSurat]);

            // ✅ Cek apakah file template ada di storage
            $templatePath = storage_path('app/' . $template->file_path);
            \Log::info('Template path', ['path' => $templatePath]);

            if (!file_exists($templatePath)) {
                \Log::error('Template file not found', ['path' => $templatePath]);
                return redirect()->route('surat.index')
                    ->with('error', 'Template surat tidak ditemukan!');
            }

            // ✅ 🔥 FIX: Buat nama file yang AMAN (hapus karakter khusus dari nomor surat)
            $safeNomorSurat = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nomorSurat);
            $safeNama = preg_replace('/[^a-zA-Z0-9_-]/', '_', $warga->nama);
            $fileName = 'surat_' . $safeNomorSurat . '_' . $safeNama . '_' . time() . '.docx';
            $outputPath = storage_path('app/generated/' . $fileName);

            \Log::info('Output path', ['path' => $outputPath, 'fileName' => $fileName]);

            // ✅ Pastikan directory generated exists
            $generatedDir = storage_path('app/generated');
            if (!file_exists($generatedDir)) {
                mkdir($generatedDir, 0755, true);
                \Log::info('Created directory', ['dir' => $generatedDir]);
            }

            // ✅ Isi template dengan data warga
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Format tanggal lahir dengan pengecekan null
             $templateProcessor = new TemplateProcessor($templatePath);
        
            // 🔥 PERBAIKAN: Format tanggal lahir dengan format "1 Maret 2013"
            $tanggalLahir = '';
            if ($warga->tanggal_lahir) {
                try {
                    // Format: "1 Maret 2013" (tanpa leading zero, bulan dalam bahasa Indonesia)
                    $tanggalLahir = \Carbon\Carbon::parse($warga->tanggal_lahir)->translatedFormat('j F Y');
                } catch (\Exception $e) {
                    // Fallback ke format default jika ada error
                    $tanggalLahir = $warga->tanggal_lahir;
                }
            }

            // 🔥 DATA LENGKAP untuk diisi ke template
            $data = [
                // ==================== DATA WARGA ====================
                'nama' => $warga->nama ?? '',
                'nik' => $warga->nik ?? '',
                'no_kk' => $warga->no_kk ?? '',
                'tempat_lahir' => $warga->tempat_lahir ?? '',
                'tanggal_lahir' => $tanggalLahir,
                'agama' => $warga->agama ?? '',
                'kewarganegaraan' => $warga->kewarganegaraan ?? 'Indonesia',
                'alamat' => $warga->alamat ?? '',
                'jenis_kelamin' => $warga->jenis_kelamin ?? '',
                'status_nikah' => $warga->status_nikah ?? '',
                'pekerjaan' => $warga->pekerjaan ?? '',
                'rt' => $warga->rt ? str_pad($warga->rt, 3, '0', STR_PAD_LEFT) : '',
                'rw' => $warga->rw ? str_pad($warga->rw, 3, '0', STR_PAD_LEFT) : '',

                // ==================== NOMOR SURAT ====================
                'nomor_surat' => $nomorSurat,
                'no_surat' => $nomorSurat,
                'nomor' => $nomorSurat,

                // ==================== TANGGAL & WAKTU ====================
                'tanggal' => now()->translatedFormat('d F Y'),
                'tahun' => now()->format('Y'),
                'bulan' => now()->translatedFormat('F'),
                'hari' => now()->translatedFormat('l'),
                'tanggal_sekarang' => now()->translatedFormat('d F Y'),
                'tahun_sekarang' => now()->format('Y'),

                // ==================== MASA BERLAKU 30 HARI ====================
                'masa_berlaku' => now()->addDays(90)->translatedFormat('d F Y'),
                'masa_berlaku_sampai' => now()->addDays(90)->translatedFormat('d F Y'),
                'berlaku_sampai' => now()->addDays(90)->translatedFormat('d F Y'),
                'tanggal_berlaku' => now()->addDays(90)->translatedFormat('d F Y'),
                'berlaku_hingga' => now()->addDays(90)->translatedFormat('d F Y'),
                'masa_berlaku_hingga' => now()->addDays(90)->translatedFormat('d F Y'),
                'berlaku_30_hari' => now()->addDays(90)->translatedFormat('d F Y'),
                'sampai_tanggal' => now()->addDays(90)->translatedFormat('d F Y'),
                'expired_date' => now()->addDays(90)->translatedFormat('d F Y'),

                // ==================== DATA DEFAULT DESA ====================
                'tujuan_surat' => 'KEPADA YTH.',
                'kepala_desa' => 'SUKIRIN',
                'jabatan_kepala_desa' => 'Kepala Desa',
                'nama_desa' => 'DESA BLAGUNG',
                'kecamatan' => 'KECAMATAN SIMO',
                'kabupaten' => 'KABUPATEN BOYOLALI',
                'provinsi' => 'PROVINSI JAWA TENGAH',
                'alamat_desa' => 'Desa Blagung, Kecamatan Simo, Kabupaten Boyolali',

                // ==================== DATA KELUARGA ====================
                'nama_kepala_keluarga' => $this->getKepalaKeluarga($warga->no_kk),
                'nik_kepala_keluarga' => $this->getNikKepalaKeluarga($warga->no_kk),
            ];

            \Log::info('Data to be filled', array_keys($data));

            // Isi semua data ke template
            foreach ($data as $key => $value) {
                try {
                    $templateProcessor->setValue($key, $value);
                    \Log::debug('Set template value', ['key' => $key, 'value' => $value]);
                } catch (\Exception $e) {
                    // Skip jika field tidak ada di template
                    \Log::warning('Failed to set template value', [
                        'key' => $key, 
                        'value' => $value,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            // ✅ Simpan hasil generate surat
            $templateProcessor->saveAs($outputPath);
            \Log::info('File saved successfully', ['path' => $outputPath]);

            // ✅ Cek apakah file benar-benar ada sebelum download
            if (!file_exists($outputPath)) {
                \Log::error('Generated file not found after save', ['path' => $outputPath]);
                throw new \Exception('File surat gagal dibuat');
            }

            \Log::info('Starting file download', ['path' => $outputPath, 'fileName' => $fileName]);

            // ✅ Download file
            return response()->download($outputPath, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Error generating surat', [
                'warga_id' => $warga_id,
                'template_id' => $template_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus file jika ada error
            if (isset($outputPath) && file_exists($outputPath)) {
                unlink($outputPath);
                \Log::info('Cleaned up failed file', ['path' => $outputPath]);
            }

            return redirect()->route('surat.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 GENERATE NOMOR SURAT dengan counter terpisah untuk setiap jenis surat
     * Format: KODE_SURAT/NOMOR_URUT/BULAN_ROMawi/TAHUN
     * Contoh: 301/1/X/2025, 302/1/X/2025, 401/1/X/2025
     */
    private function generateNomorSuratWithCounter($template)
    {
        $kodeJenis = 300; // 301, 302, 401, dll
        $tahun = date('Y');
        $bulan = date('n');
        $bulanRomawi = $this->convertToRoman($bulan);
        
        // 🔥 File counter spesifik untuk jenis surat + tahun + bulan
        $counterFile = storage_path("app/counters/{$kodeJenis}_{$tahun}_{$bulan}.txt");
        
        // Pastikan directory counters exists
        $counterDir = storage_path('app/counters');
        if (!file_exists($counterDir)) {
            mkdir($counterDir, 0755, true);
        }
        
        // 🔥 Baca atau buat counter untuk jenis surat ini
        if (file_exists($counterFile)) {
            $counter = (int) file_get_contents($counterFile);
            $counter++;
        } else {
            $counter = 1;
        }
        
        // 🔥 Simpan counter untuk jenis surat ini
        file_put_contents($counterFile, $counter);
        
        \Log::info('Counter updated', [
            'kode_jenis' => $kodeJenis,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'counter' => $counter,
            'nomor_surat' => "{$kodeJenis}/{$counter}/{$bulanRomawi}/{$tahun}"
        ]);
        
        // Format: 301/1/X/2025
        return "{$kodeJenis}/{$counter}/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Convert angka bulan ke romawi
     */
    private function convertToRoman($number)
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        
        return $romawi[$number] ?? $number;
    }

    /**
     * Live search untuk warga di form surat
     */
    public function searchWarga(Request $request)
    {
        // CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return response()->json(['error' => 'Session tidak valid'], 401);
        }

        $keyword = $request->get('q');
        
        $warga = Warga::where('nama', 'like', "%{$keyword}%")
            ->orWhere('nik', 'like', "%{$keyword}%")
            ->orWhere('no_kk', 'like', "%{$keyword}%")
            ->select('id', 'nik', 'nama', 'no_kk', 'alamat')
            ->limit(10)
            ->get();

        return response()->json($warga);
    }

    /**
     * Get nama kepala keluarga
     */
    private function getKepalaKeluarga($no_kk)
    {
        $kepalaKeluarga = Warga::where('no_kk', $no_kk)
            ->orderBy('id')
            ->first();

        return $kepalaKeluarga ? $kepalaKeluarga->nama : '';
    }

    /**
     * Get NIK kepala keluarga
     */
    private function getNikKepalaKeluarga($no_kk)
    {
        $kepalaKeluarga = Warga::where('no_kk', $no_kk)
            ->orderBy('id')
            ->first();

        return $kepalaKeluarga ? $kepalaKeluarga->nik : '';
    }

    public function monitorNomorSurat()
    {
        // CHECK SESSION VALIDITY UNTUK MULTI-LOGIN PREVENTION
        $sessionCheck = $this->checkSessionValidity();
        if ($sessionCheck) {
            return $sessionCheck;
        }

        // Ambil data templates dan filter duplikat
        $templates = Template::orderBy('nomor_surat')->get();
        
        // 🔥 FILTER DATA UNIK berdasarkan nomor_surat
        $uniqueTemplates = $templates->unique(function ($item) {
            return $item->nomor_surat . $item->nama_template;
        });
        
        // Ambil statistik lengkap
        $statistik = $this->getStatistikSurat();
        
        // Ambil data counter bulan ini untuk monitor nomor surat
        $tahun = date('Y');
        $bulan = date('n');
        $bulanRomawi = $this->convertToRoman($bulan);
        
        $statistikBulanIni = [];
        $counterDir = storage_path('app/counters');
        
        if (file_exists($counterDir)) {
            $files = glob($counterDir . "/*_{$tahun}_{$bulan}.txt");
            
            foreach ($files as $file) {
                $filename = basename($file);
                preg_match('/(\d+)_(\d+)_(\d+)\.txt/', $filename, $matches);
                
                if (count($matches) === 4) {
                    $kodeJenis = $matches[1];
                    $tahunFile = $matches[2];
                    $bulanFile = $matches[3];
                    $counter = (int) file_get_contents($file);
                    
                    // Cari template yang sesuai dari data unik
                    $template = $uniqueTemplates->firstWhere('nomor_surat', $kodeJenis);
                    
                    if ($template) {
                        $statistikBulanIni[] = [
                            'kode_surat' => $kodeJenis,
                            'nama_template' => $template->nama_template,
                            'counter' => $counter,
                            'nomor_terakhir' => "{$kodeJenis}/{$counter}/{$bulanRomawi}/{$tahun}",
                            'tahun' => $tahunFile,
                            'bulan' => $bulanFile
                        ];
                    }
                }
            }
        }
        
        // Urutkan berdasarkan kode surat
        usort($statistikBulanIni, function($a, $b) {
            return $a['kode_surat'] <=> $b['kode_surat'];
        });

        return view('surat.monitor', compact(
            'templates', 
            'uniqueTemplates', // 🔥 KIRIM DATA UNIK KE VIEW
            'statistikBulanIni', 
            'tahun', 
            'bulanRomawi',
            'statistik'
        ));
    }

/**
 * 🔥 METHOD BARU: Get Statistik Pembuatan Surat
 */
private function getStatistikSurat()
{
    $counterDir = storage_path('app/counters');
    $statistik = [
        'bulan_ini' => [],
        'tahun_ini' => [],
        'total_surat' => 0,
        'jenis_terbanyak' => null,
        'trend_bulanan' => []
    ];

    if (!file_exists($counterDir)) {
        return $statistik;
    }

    $tahunIni = date('Y');
    $bulanIni = date('n');
    
    // Ambil semua file counter
    $files = glob($counterDir . '/*.txt');
    
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Parse filename: {kode}_{tahun}_{bulan}.txt
        if (preg_match('/(\d+)_(\d+)_(\d+)\.txt/', $filename, $matches)) {
            $kode = $matches[1];
            $tahun = $matches[2];
            $bulan = $matches[3];
            $count = (int) file_get_contents($file);
            
            // Statistik bulan ini
            if ($tahun == $tahunIni && $bulan == $bulanIni) {
                $statistik['bulan_ini'][$kode] = $count;
            }
            
            // Statistik tahun ini
            if ($tahun == $tahunIni) {
                if (!isset($statistik['tahun_ini'][$kode])) {
                    $statistik['tahun_ini'][$kode] = 0;
                }
                $statistik['tahun_ini'][$kode] += $count;
            }
            
            // Trend bulanan
            $key = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
            if (!isset($statistik['trend_bulanan'][$key])) {
                $statistik['trend_bulanan'][$key] = 0;
            }
            $statistik['trend_bulanan'][$key] += $count;
            
            $statistik['total_surat'] += $count;
        }
    }

    // Cari jenis surat terbanyak
    if (!empty($statistik['tahun_ini'])) {
        arsort($statistik['tahun_ini']);
        $statistik['jenis_terbanyak'] = array_key_first($statistik['tahun_ini']);
    }

    // Urutkan trend bulanan
    ksort($statistik['trend_bulanan']);

    return $statistik;
}

/**
 * 🔥 METHOD UPDATE: Reset counter untuk jenis surat tertentu
 */
public function resetCounter(Request $request)
{
    $sessionCheck = $this->checkSessionValidity();
    if ($sessionCheck) {
        return $sessionCheck;
    }

    $kode = $request->get('kode');
    $tahun = date('Y');
    $bulan = date('n');
    
    $counterFile = storage_path("app/counters/{$kode}_{$tahun}_{$bulan}.txt");
    
    if (file_exists($counterFile)) {
        unlink($counterFile);
        \Log::info('Counter surat direset', ['kode' => $kode]);
        
        return redirect()->route('surat.monitor')
            ->with('success', "Counter untuk surat {$kode} berhasil direset");
    }
    
    return redirect()->route('surat.monitor')
        ->with('error', "Counter untuk surat {$kode} tidak ditemukan");
}

    /**
     * 🔥 METHOD BARU: Debug template fields
     */
    public function debugTemplate($template_id)
    {
        try {
            $template = Template::findOrFail($template_id);
            $templatePath = storage_path('app/' . $template->file_path);
            
            if (!file_exists($templatePath)) {
                return "Template file not found: " . $templatePath;
            }
            
            $templateProcessor = new TemplateProcessor($templatePath);
            $variables = $templateProcessor->getVariables();
            
            echo "<h3>Fields yang ada di template: " . $template->nama_template . "</h3>";
            echo "<pre>";
            print_r($variables);
            echo "</pre>";
            
            echo "<h3>Gunakan field-field ini di Word:</h3>";
            foreach($variables as $variable) {
                echo "<code>{{$variable}}</code><br>";
            }
            
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * 🔥 METHOD BARU: Show all available fields
     */
    public function showAvailableFields()
    {
        $fields = Template::getAvailableFields();
        
        return view('surat.fields', compact('fields'));
    }
}