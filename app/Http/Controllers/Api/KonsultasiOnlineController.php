<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KonsultasiOnline;
use Illuminate\Http\Request;

class KonsultasiOnlineController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'keluhan' => 'required|string|min:10',
        ]);

        $pasien = auth()->user()->pasien;

        $existing = KonsultasiOnline::where('pasien_id', $pasien->id)
            ->whereIn('status', ['menunggu', 'berlangsung'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda masih memiliki konsultasi yang aktif',
                'data' => $existing,
            ], 422);
        }

        $dokterId = 1;

        $konsultasi = KonsultasiOnline::create([
            'nomor_konsultasi' => KonsultasiOnline::generateNomorKonsultasi(),
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokterId,
            'keluhan' => $request->keluhan,
            'status' => 'menunggu',
        ]);

        return response()->json([
            'message' => 'Konsultasi berhasil dibuat',
            'data' => $konsultasi,
        ], 201);
    }

    public function index()
    {
        $pasienId = auth()->user()->pasien->id;

        $data = KonsultasiOnline::where('pasien_id', $pasienId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($data);
    }

    public function mulai($id)
    {
        $konsultasi = KonsultasiOnline::findOrFail($id);

        $konsultasi->update([
            'status' => 'berlangsung',
            'dimulai_pada' => now(),
        ]);

        return response()->json([
            'message' => 'Konsultasi dimulai',
            'data' => $konsultasi,
        ]);
    }

    public function selesai(Request $request, $id)
    {
        $konsultasi = KonsultasiOnline::findOrFail($id);

        $konsultasi->update([
            'status' => 'selesai',
            'selesai_pada' => now(),
            'biaya_konsultasi' => $request->biaya_konsultasi ?? 0,
            'catatan_dokter' => $request->catatan_dokter,
        ]);

        return response()->json([
            'message' => 'Konsultasi selesai',
            'data' => $konsultasi,
        ]);
    }
}
