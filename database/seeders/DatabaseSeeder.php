<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. INPUT DATA USER (ADMIN & PETUGAS)
        User::create([
            'name' => 'Admin Kelurahan',
            'email' => 'admin@kelurahan.id',
            'password' => Hash::make('password'), // Password: password
            'role' => 'admin',
            'is_logged_in' => false,
        ]);

        User::create([
            'name' => 'Petugas Pelayanan',
            'email' => 'petugas@example.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'petugas',
            'is_logged_in' => false,
        ]);

        // 2. INPUT DATA WARGA DUMMY
        DB::table('warga')->insert([
            [
                'nik' => '3201010101010001',
                'no_kk' => '3201010101019999',
                'nama' => 'Budi Santoso',
                'nama_ayah' => 'Slamet',
                'nama_ibu' => 'Siti',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1990-05-12',
                'alamat' => 'Jl. Merdeka No. 10',
                'rt' => 001,
                'rw' => 005,
                'jenis_kelamin' => 'Laki-laki',
                'status_nikah' => 'Kawin',
                'status_hubungan_dalam_keluarga' => 'Kepala Keluarga',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'pendidikan' => 'S1',
                'kewarganegaraan' => 'Indonesia',
                'status_hidup' => 'Hidup',
                'created_at' => now(),
            ],
            [
                'nik' => '3201010101010002',
                'no_kk' => '3201010101019999',
                'nama' => 'Siti Aminah',
                'nama_ayah' => 'Joko',
                'nama_ibu' => 'Sri',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1992-08-20',
                'alamat' => 'Jl. Merdeka No. 10',
                'rt' => 001,
                'rw' => 005,
                'jenis_kelamin' => 'Perempuan',
                'status_nikah' => 'Kawin',
                'status_hubungan_dalam_keluarga' => 'Istri',
                'agama' => 'Islam',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'pendidikan' => 'D3',
                'kewarganegaraan' => 'Indonesia',
                'status_hidup' => 'Hidup',
                'created_at' => now(),
            ]
        ]);

        // 3. INPUT TEMPLATE SURAT DUMMY
        DB::table('templates')->insert([
            [
                'kategori' => 'Kependudukan',
                'nama_template' => 'Surat Keterangan Domisili',
                'deskripsi' => 'Surat untuk keterangan tempat tinggal',
                'nomor_surat' => '470/001/2026',
                'file_path' => 'templates/domisili.docx',
                'created_at' => now(),
            ]
        ]);
    }
}