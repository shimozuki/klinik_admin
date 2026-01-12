<?php

namespace App\Exports;

use App\Models\Reservasi;
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

class BookingExport implements
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

    public function collection(): Collection
    {
        return Reservasi::with(['pasien.user', 'dokter'])
            ->when(
                $this->startDate,
                fn($q) =>
                $q->whereDate('tanggal_reservasi', '>=', $this->startDate)
            )
            ->when(
                $this->endDate,
                fn($q) =>
                $q->whereDate('tanggal_reservasi', '<=', $this->endDate)
            )
            ->orderBy('tanggal_reservasi')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Reservasi',
            'Tanggal',
            'Pasien',
            'Dokter',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nomor_reservasi,
            Carbon::parse($row->tanggal_reservasi),
            $row->pasien->user->name ?? '-',
            $row->dokter->name ?? '-',
            ucfirst($row->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    /**
     * ⬅️ INI KUNCI EXPORT
     */
    public function startCell(): string
    {
        return 'A5';
    }

    public function styles(Worksheet $sheet)
    {
        // === KOP ===
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');

        $sheet->setCellValue('A1', 'KLINIK SEHAT SENTOSA');
        $sheet->setCellValue('A2', 'LAPORAN BOOKING');
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
        $sheet->getStyle('A5:E5')->applyFromArray([
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

        $sheet->getStyle("A5:E{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }
}
