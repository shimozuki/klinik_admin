<?php
// app/Models/LayananTindakan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananTindakan extends Model
{
    use HasFactory;

    protected $table = 'layanan_tindakan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'harga',
        'estimasi_durasi',
        'status_aktif',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'estimasi_durasi' => 'integer',
        'status_aktif' => 'boolean',
    ];

    // Scope untuk layanan aktif
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    // Scope untuk layanan tidak aktif
    public function scopeTidakAktif($query)
    {
        return $query->where('status_aktif', false);
    }

    // Helper untuk format harga
    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Helper untuk format durasi
    public function getDurasiFormatAttribute(): string
    {
        if (!$this->estimasi_durasi) {
            return '-';
        }

        $jam = floor($this->estimasi_durasi / 60);
        $menit = $this->estimasi_durasi % 60;

        if ($jam > 0 && $menit > 0) {
            return $jam . ' jam ' . $menit . ' menit';
        } elseif ($jam > 0) {
            return $jam . ' jam';
        } else {
            return $menit . ' menit';
        }
    }

    // Helper untuk mendapatkan status text
    public function getStatusTextAttribute(): string
    {
        return $this->status_aktif ? 'Aktif' : 'Tidak Aktif';
    }
}
