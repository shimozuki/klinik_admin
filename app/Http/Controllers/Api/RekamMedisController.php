<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use App\Models\Reservasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RekamMedisController extends Controller
{
    /**
     * GET /api/rekam-medis/pasien/{pasienId}
     */
    public function byPasien(): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->pasien) {
            return response()->json([
                'message' => 'User bukan pasien'
            ], 403);
        }

        $pasienId = $user->pasien->id;

        $data = RekamMedis::with(['pasien.user', 'dokter', 'resepDetail'])
            ->where('pasien_id', $pasienId)
            ->orderByDesc('tanggal_pemeriksaan')
            ->get()
            ->map(fn($rm) => $this->transform($rm));

        return response()->json($data);
    }
    /**
     * GET /api/rekam-medis/reservasi/{nomorReservasi}
     */
    public function byReservasi(string $nomorReservasi): JsonResponse
    {
        $data = RekamMedis::with(['pasien.user', 'dokter', 'resepDetail'])
            ->where('nomor_rekam', $nomorReservasi)
            ->get()
            ->map(fn($rm) => $this->transform($rm));

        return response()->json($data);
    }

    /**
     * 🔥 TRANSFORM DATA (MATCH FLUTTER MODEL)
     */
    private function transform(RekamMedis $rm): array
    {
        return [
            'id' => $rm->nomor_rekam,
            'patientName' => $rm->pasien->user->name ?? '-',
            'patientId' => (string) $rm->pasien->id,
            'visitDate' => $rm->tanggal_pemeriksaan?->toIso8601String(),
            'doctorName' => $rm->dokter->name ?? '-',

            'complaint' => $rm->anamnesis,
            'diagnosis' => $rm->diagnosis,
            'treatment' => null,
            'procedures' => [],

            'prescriptions' => $rm->resepDetail->map(fn($r) => [
                'medicineName' => $r->nama_obat,
                'dosage' => $r->dosis,
                'frequency' => $r->frekuensi,
                'duration' => (int) filter_var($r->durasi, FILTER_SANITIZE_NUMBER_INT),
                'notes' => $r->catatan,
            ]),

            'notes' => $rm->catatan,
            'totalCost' => (int) $rm->biaya,
            'status' => 'completed',

            'vitalSigns' => [
                'bloodPressure' => $rm->tekanan_darah,
                'heartRate' => $rm->detak_jantung,
                'temperature' => (float) $rm->suhu,
                'weight' => (float) $rm->berat_badan,
            ],
        ];
    }
}
