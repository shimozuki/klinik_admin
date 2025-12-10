<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsultasi_online', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_konsultasi')->unique();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('keluhan');
            $table->enum('status', ['menunggu', 'berlangsung', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamp('dimulai_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();
            $table->decimal('biaya_konsultasi', 10, 2)->default(0);
            $table->text('catatan_dokter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsultasi_online');
    }
};
