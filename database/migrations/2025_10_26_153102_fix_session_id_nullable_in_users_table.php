<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom session_id menjadi nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('session_id')->nullable()->change();
        });
        
        // Set semua session_id yang kosong menjadi NULL
        DB::table('users')->where('session_id', '')->update(['session_id' => null]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('session_id')->nullable(false)->change();
        });
    }
};