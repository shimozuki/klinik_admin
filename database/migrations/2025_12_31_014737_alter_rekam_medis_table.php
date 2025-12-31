<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            if (Schema::hasColumn('rekam_medis', 'pemeriksaan_fisik')) {
                $table->dropColumn('pemeriksaan_fisik');
            }

            if (Schema::hasColumn('rekam_medis', 'terapi')) {
                $table->dropColumn('terapi');
            }

            if (Schema::hasColumn('rekam_medis', 'resep_obat')) {
                $table->dropColumn('resep_obat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->text('pemeriksaan_fisik')->nullable();
            $table->text('terapi')->nullable();
            $table->text('resep_obat')->nullable();
        });
    }
};
