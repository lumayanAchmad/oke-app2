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
        Schema::create('rencana_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_pegawai_id');
            $table->foreignId('data_pendidikan_id');
            $table->foreignId('data_pelatihan_id');
            $table->foreignId('bentuk_jalur_id');
            $table->foreignId('jenis_pendidikan_id');
            $table->foreignId('region_id');
            $table->foreignId('jenjang_id');
            $table->string('klasifikasi');
            $table->year('tahun');
            $table->integer('jam_pelajaran');
            $table->integer('anggaran_rencana');
            $table->string('prioritas');
            $table->string('status_pengajuan')->default('draft');
            $table->datetime('revisi_due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_pembelajarans');
    }
};
