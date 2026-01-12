<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LayananTindakan;
use App\Models\User;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        try {
            $data = LayananTindakan::select('nama', 'deskripsi', 'harga', 'estimasi_durasi')->get();

            return response()->json(
                [
                    'success' => true,
                    'data' => $data
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal dokter',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getDokter()
    {
        try {
            $dokter = User::role('dokter')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $dokter,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal dokter',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
