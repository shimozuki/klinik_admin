<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';

    protected $fillable = [
        'nomor_rekam',
        'pasien_id',
        'dokter_id',
        'reservasi_id',
        'tanggal_pemeriksaan',
        'anamnesis',
        'pemeriksaan_fisik',
        'diagnosis',
        'terapi',
        'resep_obat',
        'catatan',
        'biaya',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'biaya' => 'decimal:2',
    ];

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
     * Relasi ke reservasi
     */
    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public static function generateNomorRekam(): string
    {
        $date = now()->format('Ymd');
        $lastRekam = static::whereDate('created_at', now())->latest()->first();

        if ($lastRekam) {
            $lastNumber = (int) substr($lastRekam->nomor_rekam, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RM-' . $date . '-' . $newNumber;
    }

    public function getBiayaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }
}
