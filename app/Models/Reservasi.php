<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';

    protected $fillable = [
        'nomor_reservasi',
        'pasien_id',
        'dokter_id',
        'jadwal_id',
        'tanggal_reservasi',
        'jam_reservasi',
        'nomor_antrian',
        'status',
        'keluhan',
        'catatan',
        'dikonfirmasi_pada',
        'dibatalkan_pada',
        'alasan_batal',
    ];

    protected $casts = [
        'tanggal_reservasi' => 'date',
        'jam_reservasi'     => 'datetime:H:i',
        'dikonfirmasi_pada' => 'datetime',
        'dibatalkan_pada'   => 'datetime',
    ];

    public static function generateNomorReservasi(): string
    {
        $date = now()->format('Ymd');
        $lastReservasi = static::whereDate('created_at', now())->latest()->first();

        if ($lastReservasi) {
            $lastNumber = (int) substr($lastReservasi->nomor_reservasi, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RSV-' . $date . '-' . $newNumber;
    }

    /**
     * Relasi ke pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Relasi ke dokter (users)
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    /**
     * Relasi ke jadwal dokter
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_id');
    }
}
