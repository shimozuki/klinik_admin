<?php

namespace App\Services;

use App\Models\Reservasi;

class ReservasiService
{
    public static function generateNomorReservasi(): string
    {
        $date = now()->format('Ymd');

        $count = Reservasi::whereDate('created_at', today())
            ->lockForUpdate()
            ->count() + 1;

        return 'RSV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateNomorAntrian(
        int $dokterId,
        int $jadwalId,
        string $tanggalReservasi
    ): int {
        $lastQueue = Reservasi::where('dokter_id', $dokterId)
            ->where('jadwal_id', $jadwalId)
            ->whereDate('tanggal_reservasi', $tanggalReservasi)
            ->lockForUpdate()
            ->max('nomor_antrian');

        return ($lastQueue ?? 0) + 1;
    }
}
