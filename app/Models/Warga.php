<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';
    
    protected $fillable = [
    'nik',
    'no_kk', 
    'nama',
    'nama_ayah',
    'nama_ibu',
    'tempat_lahir',
    'tanggal_lahir', 
    'alamat',
    'rt',
    'rw',
    'jenis_kelamin',
    'status_nikah',
    'status_hubungan_dalam_keluarga',
    'status_hidup',
    'agama',
    'pekerjaan',
    'pendidikan',
    'kewarganegaraan',
];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationship untuk anggota KK yang sama
    public function anggota()
    {
        return $this->hasMany(Warga::class, 'no_kk', 'no_kk');
    }

    // Scope untuk kepala keluarga
    public function scopeKepalaKeluarga($query)
    {
        return $query->whereNotNull('no_kk')
                    ->groupBy('no_kk');
    }
}