<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use App\Models\Reservasi;
use App\Models\User;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    public function index()
    {
        try {
            $data = Reservasi::with(['dokter', 'jadwal', 'pasien'])
                ->where('pasien_id', auth()->user()->pasien->id)
                ->get();
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data reservasi',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal_dokter,id',
                'keluhan' => 'nullable|string',
            ]);

            $reservasi = DB::transaction(function () use ($request) {
                $dokter = User::whereHas('roles', fn($q) => $q->where('name', 'dokter'))
                    ->firstOrFail();

                $jadwal = JadwalDokter::findOrFail($request->jadwal_id);

                $tanggalReservasi = now()->toDateString();
                $jamReservasi = $jadwal->jam_mulai;

                $nomorReservasi = ReservasiService::generateNomorReservasi();

                $nomorAntrian = ReservasiService::generateNomorAntrian(
                    $dokter->id,
                    $jadwal->id,
                    $tanggalReservasi
                );

                return Reservasi::create([
                    'nomor_reservasi' => $nomorReservasi,
                    'pasien_id' => auth()->user()->pasien->id,
                    'dokter_id' => $dokter->id,
                    'jadwal_id' => $jadwal->id,
                    'tanggal_reservasi' => $tanggalReservasi,
                    'jam_reservasi' => $jamReservasi,
                    'nomor_antrian' => $nomorAntrian,
                    'status' => 'menunggu',
                    'keluhan' => $request->keluhan,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibuat',
                'data' => $reservasi,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal buat reservasi',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }
}
