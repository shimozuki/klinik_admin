<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DentalVisitController extends Controller
{
    public function index()
    {
        $data = DB::table('rekam_medis')
            ->join('pasien', 'pasien.id', '=', 'rekam_medis.pasien_id')
            ->join('users as pasien_user', 'pasien_user.id', '=', 'pasien.user_id')
            ->leftJoin('reservasi', 'reservasi.id', '=', 'rekam_medis.reservasi_id')
            ->leftJoin('layanan_tindakan', 'layanan_tindakan.id', '=', 'reservasi.layanan_id')
            ->select([
                'rekam_medis.id',
                'pasien_user.name as patientName',
                'pasien.nomor_rekam_medis as patientId',
                'rekam_medis.tanggal_pemeriksaan',
                'reservasi.jam_reservasi',
                'layanan_tindakan.nama as treatmentType',
                'rekam_medis.diagnosis',
                'rekam_medis.anamnesis',
                'rekam_medis.catatan',
                'rekam_medis.biaya',
                'reservasi.status'
            ])
            ->orderBy('rekam_medis.tanggal_pemeriksaan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data->map(function ($row) {
                return [
                    'id' => (string) $row->id,
                    'patientName' => $row->patientName,
                    'patientId' => $row->patientId,
                    'visitDate' => $row->tanggal_pemeriksaan,
                    'visitTime' => $row->jam_reservasi
                        ? substr($row->jam_reservasi, 0, 5)
                        : null,
                    'treatmentType' => $row->treatmentType ?? '-',
                    'diagnosis' => $row->diagnosis,
                    'treatment' => $row->anamnesis,
                    'teethNumbers' => [], // future JSON
                    'notes' => $row->catatan,
                    'treatmentCost' => (float) $row->biaya,
                    'consultationFee' => 100000,
                    'additionalCost' => 0,
                    'paymentMethod' => 'cash',
                    'status' => $this->mapStatus($row->status),
                    'nextAppointment' => null
                ];
            })
        ]);
    }

    private function mapStatus($status)
    {
        return match ($status) {
            'selesai' => 'completed',
            'dibatalkan' => 'cancelled',
            default => 'scheduled'
        };
    }
}
