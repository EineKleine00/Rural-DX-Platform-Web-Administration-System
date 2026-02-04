<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('nama_template', 100);
            $table->string('deskripsi')->nullable();
            $table->string('nomor_surat', 20)->nullable();
            $table->string('file_path'); // Lokasi file surat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};