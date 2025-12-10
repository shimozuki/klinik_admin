<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'user_id',
        'nomor_rekam_medis',
        'nik',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'kontak_darurat',
        'telepon_darurat',
        'golongan_darah',
        'alergi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Reservasi
     */
    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }

    /**
     * Relasi ke Rekam Medis
     */
    public function rekamMedis(): HasMany
    {
        return $this->hasMany(RekamMedis::class);
    }

    /**
     * Relasi ke Konsultasi Online
     */
    public function konsultasiOnline(): HasMany
    {
        return $this->hasMany(KonsultasiOnline::class);
    }

    /**
     * Accessor untuk mendapatkan umur
     */
    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dari user
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Generate nomor rekam medis otomatis
     */
    public static function generateNomorRekamMedis(): string
    {
        $lastPasien = self::latest('id')->first();
        $number = $lastPasien ? $lastPasien->id + 1 : 1;
        return 'RM' . date('Ym') . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
