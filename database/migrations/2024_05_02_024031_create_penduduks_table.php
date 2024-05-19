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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('No_KK', 100);
            $table->bigInteger('NIK')->unique();
            $table->string('Nama_lengkap', 150);
            $table->string('Hbg_kel', 50);
            $table->string('JK', 50);
            $table->string('tmpt_lahir', 50);
            $table->date('tgl_lahir');
            $table->string('Agama', 100);
            $table->string('Pendidikan_terakhir', 50);
            $table->string('Jenis_bantuan', 50);
            $table->string('Penerima_bantuan', 50);
            $table->string('Jenis_bantuan_lain', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
