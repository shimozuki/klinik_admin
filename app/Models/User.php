<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // TAMBAHKAN INI
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Pasien;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'specialization',
        'license_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke Pasien (jika user adalah pasien)
     */
    public function pasien(): HasOne
    {
        return $this->hasOne(Pasien::class);
    }

    /**
     * Relasi ke Jadwal Dokter (jika user adalah dokter)
     */
    public function jadwalDokter(): HasMany
    {
        return $this->hasMany(JadwalDokter::class, 'dokter_id');
    }

    /**
     * Relasi ke Reservasi sebagai dokter
     */
    public function reservasiDokter(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'dokter_id');
    }

    /**
     * Relasi ke Rekam Medis sebagai dokter
     */
    public function rekamMedisDokter(): HasMany
    {
        return $this->hasMany(RekamMedis::class, 'dokter_id');
    }

    /**
     * Relasi ke Konsultasi Online sebagai dokter
     */
    public function konsultasiDokter(): HasMany
    {
        return $this->hasMany(KonsultasiOnline::class, 'dokter_id');
    }

    /**
     * Check apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check apakah user adalah dokter
     */
    public function isDokter(): bool
    {
        return $this->hasRole('dokter');
    }

    /**
     * Check apakah user adalah pasien
     */
    public function isPasien(): bool
    {
        return $this->hasRole('pasien');
    }

    /**
     * Scope untuk user dokter
     */
    public function scopeDokter($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'dokter');
        });
    }

    /**
     * Scope untuk user pasien
     */
    public function scopePasien($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'pasien');
        });
    }

    /**
     * Scope untuk user aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('active_status', true);
    }
}
