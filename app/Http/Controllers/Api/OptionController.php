<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LayananTindakan;
use App\Models\User;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function listDokter()
    {
        try {
            $data = User::whereHas('roles', function ($query) {
                $query->where('name', 'dokter');
            })->get(['id', 'name', 'email']);

            return response()->json(
                [
                    'success' => true,
                    'data' => $data
                ]
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal dokter',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    public function listLayanan()
    {
        try {
            $data = LayananTindakan::where('status_aktif', true)
                ->get(['id', 'kode', 'nama', 'harga', 'estimasi_durasi']);

            return response()->json(
                [
                    'success' => true,
                    'data' => $data
                ]
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data layanan tindakan',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
