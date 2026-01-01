<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index()
    {
        $pasienId = auth()->user()->pasien->id;

        $data = DB::table('rekam_medis as rm')
            ->join('pasien as p', 'p.id', '=', 'rm.pasien_id')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('reservasi as r', 'r.id', '=', 'rm.reservasi_id')
            ->leftJoin('layanan_tindakan as lt', 'lt.id', '=', 'r.layanan_id')
            ->where('rm.pasien_id', $pasienId)
            ->select([
                'rm.id',
                'u.name as patientName',
                'p.nomor_rekam_medis as patientId',
                'rm.tanggal_pemeriksaan as visitDate',
                'r.jam_reservasi as visitTime',
                'lt.nama as treatmentType',
                'rm.diagnosis',
                'rm.treatment',
                'rm.catatan as notes',
                'rm.biaya_treatment as treatmentCost',
                'rm.biaya_konsultasi as consultationFee',
                'rm.biaya as totalCost',
                'r.status',
                'r.tanggal_reservasi as baseNextAppointment'
            ])
            ->orderBy('rm.tanggal_pemeriksaan', 'desc')
            ->get();

        return response()->json(
            $data->map(function ($item) {

                $nextAppointment = null;
                if ($item->baseNextAppointment) {
                    $nextAppointment = Carbon::parse($item->baseNextAppointment)
                        ->addDays(7)
                        ->format('Y-m-d');
                }

                return [
                    'id' => (string) $item->id,
                    'patientName' => $item->patientName,
                    'patientId' => $item->patientId,
                    'visitDate' => $item->visitDate,
                    'visitTime' => $item->visitTime ?? '-',
                    'treatmentType' => $item->treatmentType ?? 'Konsultasi',
                    'diagnosis' => $item->diagnosis,
                    'treatment' => $item->treatment,
                    'notes' => $item->notes,
                    'treatmentCost' => (int) $item->treatmentCost,
                    'consultationFee' => (int) $item->consultationFee,
                    'additionalCost' => $item->totalCost,
                    'paymentMethod' => 'cash',
                    'status' => $item->status ?? 'completed',
                    'nextAppointment' => $nextAppointment,
                ];
            })
        );
    }
}
