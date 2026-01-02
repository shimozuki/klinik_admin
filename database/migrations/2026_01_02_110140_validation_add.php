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
        Schema::table('jadwal_dokter', function (Blueprint $table) {
            $table->dropForeign(['dokter_id']);

            $table->dropUnique('jadwal_dokter_dokter_id_hari_unique');

            $table->unique(
                ['dokter_id', 'hari', 'jam_mulai', 'jam_selesai'],
                'jadwal_dokter_shift_unique'
            );

            $table->foreign('dokter_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
