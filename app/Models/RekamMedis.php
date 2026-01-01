<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservasi;   // ✅ TAMBAHKAN INI
use App\Models\Pasien;
use App\Models\User;
use App\Models\ResepDetail;

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
        'diagnosis',
        'tekanan_darah',
        'detak_jantung',
        'suhu',
        'berat_badan',
        'catatan',
        'biaya',
        'treatment',
        'biaya_treatment',
        'biaya_konsultasi',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'biaya' => 'decimal:2',
        'biaya_treatment' => 'decimal:2',
        'biaya_konsultasi' => 'decimal:2',
        'suhu' => 'decimal:1',
        'berat_badan' => 'decimal:2',
    ];

    // ================= RELATIONS =================

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'reservasi_id');
    }

    public function resepDetail()
    {
        return $this->hasMany(ResepDetail::class);
    }

    // ================= HELPER =================

    public static function generateNomorRekam(): string
    {
        $date = now()->format('Ymd');
        $lastRekam = static::whereDate('created_at', now())->latest()->first();

        $newNumber = $lastRekam
            ? str_pad(((int) substr($lastRekam->nomor_rekam, -4)) + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        return 'RM-' . $date . '-' . $newNumber;
    }

    public function getBiayaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya, 0, ',', '.');
    }

    protected static function booted()
    {
        static::saving(function ($rekamMedis) {
            $rekamMedis->biaya =
                ($rekamMedis->biaya_treatment ?? 0) +
                ($rekamMedis->biaya_konsultasi ?? 0);
        });
    }
}
