<?php

namespace App\Http\Controllers\Api;

use App\Filament\Resources\Reservasis\ReservasiResource;
use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use App\Models\Reservasi;
use App\Models\User;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ReservasiController extends Controller
{
    public function index()
    {
        try {
            $data = Reservasi::query()
                ->where('pasien_id', auth()->user()->pasien->id)
                ->with([
                    'dokter:id,name,specialization',
                    'jadwal' => function ($q) {
                        $q->join('users as dokter', 'dokter.id', '=', 'jadwal_dokter.dokter_id')
                            ->select([
                                'jadwal_dokter.id',
                                'dokter.name as nama_dokter',
                                'jadwal_dokter.hari',
                                'jadwal_dokter.jam_mulai',
                                'jadwal_dokter.jam_selesai',
                                'jadwal_dokter.kuota',
                                'jadwal_dokter.status_aktif',
                            ]);
                    },
                    'layanan:id,nama'
                ])
                ->select([
                    'id',
                    'nomor_reservasi',
                    'pasien_id',
                    'dokter_id',
                    'jadwal_id',
                    'tanggal_reservasi',
                    'layanan_id',
                    'jam_reservasi',
                    'nomor_antrian',
                    'status',
                    'keluhan',
                ])
                ->orderByDesc('tanggal_reservasi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data reservasi',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal_dokter,id',
                'keluhan' => 'nullable|string',
                'layanan_id' => 'nullable|exists:layanan_tindakan,id',
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
                    'layanan_id' => $request->layanan_id,
                ]);
            });

            User::role('admin', 'web')->each(function ($admin) use ($reservasi) {
                Notification::make()
                    ->title('Reservasi Baru')
                    ->body(
                        'Pasien: ' . ($reservasi->pasien->user->name ?? '-') .
                            "\nTanggal: " . $reservasi->tanggal_reservasi
                    )
                    ->icon('heroicon-o-calendar-days')
                    ->success()
                    ->actions([
                        Action::make('lihat')
                            ->label('Lihat')
                            ->url(
                                ReservasiResource::getUrl('index')
                            )
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin, isEventDispatched: true);
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

    public function batalkan($id)
    {
        try {
            $reservasi = Reservasi::where('id', $id)
                ->where('pasien_id', auth()->user()->pasien->id)
                ->firstOrFail();

            if (in_array($reservasi->status, ['selesai', 'dibatalkan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak dapat dibatalkan',
                ], 422);
            }

            $reservasi->update([
                'status' => 'dibatalkan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibatalkan',
                'data' => $reservasi,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan',
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan reservasi',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
