<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rekam')->unique();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reservasi_id')->nullable()->constrained('reservasi')->onDelete('set null');
            $table->date('tanggal_pemeriksaan');
            $table->text('anamnesis'); // Riwayat penyakit/keluhan
            $table->text('pemeriksaan_fisik')->nullable();
            $table->text('diagnosis');
            $table->text('terapi')->nullable(); // Terapi/tindakan
            $table->text('resep_obat')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('biaya', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
