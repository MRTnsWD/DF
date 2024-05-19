<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kondisi_rumahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penduduk');
            $table->integer('Luas_lantai');
            $table->string('Jenis_lantai', 50);
            $table->string('Jenis_dinding', 50);
            $table->string('Fasilitas_BAB', 50);
            $table->string('Penerangan', 50);
            $table->string('Air_minum', 50);
            $table->string('BB_masak', 50);
            $table->string('foto_rumah', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_rumahs');
    }
};
