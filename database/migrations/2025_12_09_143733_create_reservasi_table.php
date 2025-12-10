<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_reservasi')->unique();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal_dokter')->onDelete('cascade');
            $table->date('tanggal_reservasi');
            $table->time('jam_reservasi');
            $table->integer('nomor_antrian');
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->text('keluhan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('dikonfirmasi_pada')->nullable();
            $table->timestamp('dibatalkan_pada')->nullable();
            $table->string('alasan_batal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
