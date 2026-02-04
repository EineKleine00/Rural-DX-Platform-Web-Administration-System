<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('no_kk', 20)->nullable();
            $table->string('nama', 100);
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->integer('rt');
            $table->integer('rw');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('status_nikah', ['Belum Kawin', 'Kawin', 'Cerai', 'Cerai Mati'])->default('Belum Kawin');
            $table->string('status_hubungan_dalam_keluarga');
            $table->enum('status_hidup', ['Hidup', 'Meninggal'])->default('Hidup');
            $table->enum('agama', ['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->string('pekerjaan');
            $table->string('pendidikan');
            $table->string('kewarganegaraan')->default('Indonesia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};