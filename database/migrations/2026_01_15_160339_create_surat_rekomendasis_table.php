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
        Schema::create('surat_rekomendasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_pembelajaran_id');
            $table->string('nomor_surat')->unique();
            $table->string('kode_verifikasi')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_rekomendasis');
    }
};
