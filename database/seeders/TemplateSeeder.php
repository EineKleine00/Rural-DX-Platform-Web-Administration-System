<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'kategori' => 'Surat Keterangan',
                'nama_template' => 'Surat Keterangan (Umum)',
                'deskripsi' => 'Surat keterangan umum',
                'file_path' => 'templates/surat_keterangan_umum.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Keterangan',
                'nama_template' => 'Surat Keterangan Tidak Mampu',
                'deskripsi' => 'Surat keterangan tidak mampu',
                'file_path' => 'templates/surat_keterangan_tidak_mampu.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Keterangan',
                'nama_template' => 'Surat Keterangan Domisili Tempat Tinggal',
                'deskripsi' => 'Surat domisili tempat tinggal',
                'file_path' => 'templates/surat_keterangan_domisili_tempat_tinggal.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Keterangan',
                'nama_template' => 'Surat Keterangan Usaha',
                'deskripsi' => 'Surat keterangan usaha',
                'file_path' => 'templates/surat_keterangan_usaha.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Keterangan',
                'nama_template' => 'Surat Keterangan Domisili Usaha',
                'deskripsi' => 'Surat domisili usaha',
                'file_path' => 'templates/surat_keterangan_domisili_usaha.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pengantar',
                'nama_template' => 'Surat Pengantar (Umum)',
                'deskripsi' => 'Surat pengantar umum',
                'file_path' => 'templates/surat_pengantar_umum.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pengantar',
                'nama_template' => 'Surat Pengantar Catatan Kepolisian',
                'deskripsi' => 'Surat pengantar SKCK',
                'file_path' => 'templates/surat_pengantar_skck.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pengantar',
                'nama_template' => 'Surat Pengantar Ijin Keramaian',
                'deskripsi' => 'Surat ijin keramaian',
                'file_path' => 'templates/surat_pengantar_ijin_keramaian.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pernyataan',
                'nama_template' => 'Surat Pernyataan (Umum)',
                'deskripsi' => 'Surat pernyataan umum',
                'file_path' => 'templates/surat_pernyataan_umum.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pernyataan',
                'nama_template' => 'Surat Pernyataan Ahli Waris',
                'deskripsi' => 'Surat pernyataan ahli waris',
                'file_path' => 'templates/surat_pernyataan_ahli_waris.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori' => 'Surat Pernyataan',
                'nama_template' => 'Surat Pernyataan Domisili Tempat Tinggal',
                'deskripsi' => 'Surat pernyataan domisili tempat tinggal',
                'file_path' => 'templates/surat_pernyataan_domisili_tempat_tinggal.docx',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('templates')->insert($templates);
    }
}