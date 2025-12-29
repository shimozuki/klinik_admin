<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        try {
            $data = JadwalDokter::query()
                ->where('status_aktif', true)
                ->join('users', 'users.id', '=', 'jadwal_dokter.dokter_id')
                ->select([
                    'jadwal_dokter.id',
                    'users.name as nama_dokter',
                    'jadwal_dokter.hari',
                    'jadwal_dokter.jam_mulai',
                    'jadwal_dokter.jam_selesai',
                    'jadwal_dokter.kuota',
                    'jadwal_dokter.status_aktif',
                ])
                ->orderBy('users.name')
                ->orderBy('jadwal_dokter.hari')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal dokter',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
