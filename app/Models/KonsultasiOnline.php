<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class KonsultasiOnline extends Model
{
    use HasFactory;

    protected $table = 'konsultasi_online';

    protected $fillable = [
        'nomor_konsultasi',
        'pasien_id',
        'dokter_id',
        'keluhan',
        'status',
        'dimulai_pada',
        'selesai_pada',
        'biaya_konsultasi',
        'catatan_dokter',
    ];

    protected $casts = [
        'dimulai_pada'     => 'datetime',
        'selesai_pada'     => 'datetime',
        'biaya_konsultasi' => 'decimal:2',
    ];

    /**
     * Relasi ke pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    /**
     * Relasi ke dokter
     * Dokter boleh null (jika belum ditugaskan)
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public static function generateNomorKonsultasi()
    {
        $today = Carbon::now()->format('Ymd');

        // Ambil nomor terakhir hari ini
        $last = self::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();

        if ($last && $last->nomor_konsultasi) {
            $lastNumber = intval(substr($last->nomor_konsultasi, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'KO-' . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
