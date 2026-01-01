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
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->text('treatment')
                ->after('diagnosis');
            $table->decimal('biaya_treatment', 10, 2)
                ->default(0)
                ->after('treatment');
            $table->decimal('biaya_konsultasi', 10, 2)
                ->default(0)
                ->after('biaya_treatment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn([
                'treatment',
                'biaya_treatment',
                'biaya_konsultasi',
            ]);
        });
    }
};
