<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepDetail extends Model
{
    protected $table = 'resep_detail';

    protected $fillable = [
        'rekam_medis_id',
        'nama_obat',
        'dosis',
        'frekuensi',
        'durasi',
        'catatan',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
