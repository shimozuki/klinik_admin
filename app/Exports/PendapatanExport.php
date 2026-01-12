<?php

namespace App\Exports;

use App\Models\RekamMedis;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\{
    Border,
    Alignment,
    NumberFormat
};
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithCustomStartCell
};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PendapatanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithCustomStartCell
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    /**
     * DATA (SAMA DENGAN PDF)
     */
    public function collection(): Collection
    {
        return RekamMedis::with([
            'reservasi',
            'pasien.user',
            'dokter',
        ])
            ->when(
                $this->startDate,
                fn($q) =>
                $q->whereDate('tanggal_pemeriksaan', '>=', $this->startDate)
            )
            ->when(
                $this->endDate,
                fn($q) =>
                $q->whereDate('tanggal_pemeriksaan', '<=', $this->endDate)
            )
            ->orderBy('tanggal_pemeriksaan')
            ->get();
    }

    /**
     * HEADER KOLOM (SAMA DENGAN PDF)
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Reservasi',
            'Pasien',
            'Dokter',
            'Pendapatan',
        ];
    }

    /**
     * ISI DATA PER BARIS
     */
    public function map($row): array
    {
        static $no = 1;

        return [
            $no++,
            Carbon::parse($row->tanggal_pemeriksaan),
            $row->reservasi->nomor_reservasi ?? '-',
            $row->pasien->user->name ?? '-',
            $row->dokter->name ?? '-',
            $row->biaya,
        ];
    }

    /**
     * FORMAT KOLOM EXCEL
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * START HEADER DI ROW 5
     */
    public function startCell(): string
    {
        return 'A5';
    }

    /**
     * STYLING EXCEL (SAMA DENGAN BOOKING)
     */
    public function styles(Worksheet $sheet)
    {
        // === KOP ===
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        $sheet->setCellValue('A1', 'KLINIK SEHAT SENTOSA');
        $sheet->setCellValue('A2', 'LAPORAN PENDAPATAN');
        $sheet->setCellValue(
            'A3',
            'PERIODE: ' .
                ($this->startDate ? Carbon::parse($this->startDate)->format('d-m-Y') : '-') .
                ' s/d ' .
                ($this->endDate ? Carbon::parse($this->endDate)->format('d-m-Y') : '-')
        );

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // === HEADER TABLE ===
        $sheet->getStyle('A5:F5')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // === BORDER DATA ===
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A5:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }
}
