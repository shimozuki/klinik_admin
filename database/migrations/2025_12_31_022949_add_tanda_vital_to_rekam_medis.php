<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('tekanan_darah')->nullable()->after('diagnosis');
            $table->integer('detak_jantung')->nullable()->after('tekanan_darah');
            $table->decimal('suhu', 4, 1)->nullable()->after('detak_jantung');
            $table->decimal('berat_badan', 5, 2)->nullable()->after('suhu');
        });
    }

    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn([
                'tekanan_darah',
                'detak_jantung',
                'suhu',
                'berat_badan',
            ]);
        });
    }
};
