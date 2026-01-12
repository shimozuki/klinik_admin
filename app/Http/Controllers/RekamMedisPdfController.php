<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RekamMedis;
use Barryvdh\DomPDF\Facade\Pdf;

class RekamMedisPdfController extends Controller
{
    public function generate(RekamMedis $rekamMedis)
    {
        $pdf = Pdf::loadView('pdf.rekam-medis', [
            'rm' => $rekamMedis
        ])->setPaper('A4');

        return $pdf->stream(
            'Rekam-Medis-' . $rekamMedis->nomor_rekam . '.pdf'
        );
    }
}
