<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rekam_medis_id')
                ->constrained('rekam_medis')
                ->cascadeOnDelete();

            $table->string('nama_obat');
            $table->string('dosis');
            $table->string('frekuensi');
            $table->string('durasi');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
