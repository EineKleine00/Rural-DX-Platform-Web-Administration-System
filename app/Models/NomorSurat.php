<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorSurat extends Model
{
    use HasFactory;

    protected $table = 'nomor_surat';
    
    protected $fillable = [
        'kode_surat',
        'tahun',
        'bulan',
        'nomor_urut',
        'keterangan'
    ];

    /**
     * Generate nomor surat otomatis sesuai format: 301/14/IX/2025
     * Berdasarkan template spesifik
     */
    public static function generateNomorSurat($templateId, $keterangan = null)
    {
        // Ambil template untuk dapatkan nomor_surat yang spesifik
        $template = \App\Models\Template::find($templateId);
        if (!$template) {
            throw new \Exception('Template tidak ditemukan');
        }

        $kodeJenis = $template->nomor_surat; // 301, 302, 401, dll (spesifik per template)
        $tahun = date('Y');
        $bulan = date('n');
        
        // Kode_surat sekarang menggunakan nomor_surat dari template
        $nomorSurat = self::firstOrCreate(
            [
                'kode_surat' => $kodeJenis, // 301, 302, dll
                'tahun' => $tahun,
                'bulan' => $bulan
            ],
            [
                'nomor_urut' => 0,
                'keterangan' => $keterangan ?: $template->nama_template
            ]
        );

        // Increment nomor urut
        $nomorSurat->increment('nomor_urut');
        
        // Format: 301/14/IX/2025
        $nomorUrut = $nomorSurat->nomor_urut;
        $bulanRomawi = self::convertToRoman($bulan);
        
        return "{$kodeJenis}/{$nomorUrut}/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Convert angka bulan ke romawi
     */
    public static function convertToRoman($number)
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        
        return $romawi[$number] ?? $number;
    }

    /**
     * Get riwayat nomor surat
     */
    public static function getRiwayatNomorSurat()
    {
        return self::with('template')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('kode_surat')
            ->get();
    }

    /**
     * Get statistik nomor surat bulan ini
     */
    public static function getStatistikBulanIni()
    {
        $tahun = date('Y');
        $bulan = date('n');

        return self::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->orderBy('kode_surat')
            ->get();
    }
}