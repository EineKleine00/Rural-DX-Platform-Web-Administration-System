<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'nama_template', 
        'deskripsi',
        'nomor_surat',
        'file_path'
    ];

    /**
     * Get kode surat berdasarkan template
     */
    public function getKodeSurat()
    {
        return $this->nomor_surat;
    }

    /**
     * Get template by kategori untuk dropdown
     */
    public static function getTemplatesByKategori()
    {
        return self::select('id', 'kategori', 'nama_template', 'nomor_surat')
            ->orderBy('kategori')
            ->orderBy('nomor_surat')
            ->get()
            ->groupBy('kategori');
    }

    /**
     * Get template options untuk JavaScript
     */
    public static function getTemplateOptionsForJS()
    {
        $templates = self::getTemplatesByKategori();
        $options = [];

        foreach ($templates as $kategori => $items) {
            $kategoriKey = strtolower(str_replace(' ', '_', $kategori));
            $options[$kategoriKey] = [];
            
            foreach ($items as $template) {
                $options[$kategoriKey][] = [
                    'id' => $template->id,
                    'nama' => $template->nomor_surat . ' - ' . $template->nama_template,
                    'kode' => $template->nomor_surat
                ];
            }
        }

        return $options;
    }

    /**
     * 🔥 METHOD BARU: Get available fields untuk template
     */
    public static function getAvailableFields()
    {
        return [
            '📝 DATA WARGA' => [
                'nama', 'nik', 'no_kk', 'tempat_lahir', 'tanggal_lahir',
                'agama', 'kewarganegaraan', 'alamat', 'jenis_kelamin', 
                'status_nikah', 'pekerjaan', 'rt', 'rw'
            ],
            '📅 TANGGAL & WAKTU' => [
                'tanggal', 'tahun', 'bulan', 'hari', 'tanggal_sekarang', 'tahun_sekarang'
            ],
            '🔢 NOMOR SURAT' => [
                'nomor_surat', 'no_surat', 'nomor'
            ],
            '⏰ MASA BERLAKU (30 Hari)' => [
                'masa_berlaku', 'masa_berlaku_sampai', 'berlaku_sampai',
                'tanggal_berlaku', 'berlaku_hingga', 'masa_berlaku_hingga',
                'berlaku_30_hari', 'sampai_tanggal', 'expired_date'
            ],
            '👨‍👩‍👧‍👦 DATA KELUARGA' => [
                'nama_kepala_keluarga', 'nik_kepala_keluarga'
            ],
            '🏛️ DATA DEFAULT DESA' => [
                'tujuan_surat', 'kepala_desa', 'jabatan_kepala_desa',
                'nama_desa', 'kecamatan', 'kabupaten', 'provinsi', 'alamat_desa'
            ]
        ];
    }

    /**
     * 🔥 METHOD BARU: Get statistik template
     */
    public static function getStatistikTemplate()
    {
        return self::select('nomor_surat', 'nama_template', 'kategori')
            ->orderBy('nomor_surat')
            ->get();
    }
}