<?php

namespace App\Filament\Widgets;

use App\Models\RekamMedis;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PendapatanChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pendapatan Per Bulan';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;


    protected function getData(): array
    {
        $data = $this->getPendapatanPerBulan();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data['data'],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // green
                        'rgba(59, 130, 246, 0.8)',  // blue
                        'rgba(251, 146, 60, 0.8)',  // orange
                        'rgba(168, 85, 247, 0.8)',  // purple
                        'rgba(236, 72, 153, 0.8)',  // pink
                        'rgba(14, 165, 233, 0.8)',  // sky
                        'rgba(132, 204, 22, 0.8)',  // lime
                        'rgba(245, 158, 11, 0.8)',  // amber
                        'rgba(239, 68, 68, 0.8)',   // red
                        'rgba(99, 102, 241, 0.8)',  // indigo
                        'rgba(20, 184, 166, 0.8)',  // teal
                        'rgba(244, 63, 94, 0.8)',   // rose
                    ],
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // atau 'line' jika ingin grafik garis
    }

    private function getPendapatanPerBulan(): array
    {
        $bulanIni = now();
        $labels = [];
        $data = [];

        // Ambil data 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $bulan = $bulanIni->copy()->subMonths($i);

            $pendapatan = RekamMedis::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->sum('biaya');

            $labels[] = $bulan->format('M Y');
            $data[] = (float) $pendapatan;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // Filter untuk melihat data berbeda
    protected function getFilters(): ?array
    {
        return [
            '12_bulan' => '12 Bulan Terakhir',
            'tahun_ini' => 'Tahun Ini',
            'tahun_lalu' => 'Tahun Lalu',
            'perbandingan' => 'Perbandingan Tahun',
        ];
    }

    protected function applyFilter(array $data, string $filter): array
    {
        return match ($filter) {
            'tahun_ini' => $this->getPendapatanTahunIni(),
            'tahun_lalu' => $this->getPendapatanTahunLalu(),
            'perbandingan' => $this->getPerbandinganTahun(),
            default => $this->getPendapatanPerBulan(),
        };
    }

    private function getPendapatanTahunIni(): array
    {
        $labels = [];
        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pendapatan = RekamMedis::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $bulan)
                ->sum('biaya');

            $labels[] = now()->month($bulan)->format('M');
            $data[] = (float) $pendapatan;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getPendapatanTahunLalu(): array
    {
        $labels = [];
        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pendapatan = RekamMedis::whereYear('created_at', now()->subYear()->year)
                ->whereMonth('created_at', $bulan)
                ->sum('biaya');

            $labels[] = now()->month($bulan)->format('M');
            $data[] = (float) $pendapatan;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getPerbandinganTahun(): array
    {
        $labels = [];
        $dataTahunIni = [];
        $dataTahunLalu = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pendapatanIni = RekamMedis::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $bulan)
                ->sum('biaya');

            $pendapatanLalu = RekamMedis::whereYear('created_at', now()->subYear()->year)
                ->whereMonth('created_at', $bulan)
                ->sum('biaya');

            $labels[] = now()->month($bulan)->format('M');
            $dataTahunIni[] = (float) $pendapatanIni;
            $dataTahunLalu[] = (float) $pendapatanLalu;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tahun ' . now()->year,
                    'data' => $dataTahunIni,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
                [
                    'label' => 'Tahun ' . now()->subYear()->year,
                    'data' => $dataTahunLalu,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'interaction' => [
                'mode' => 'nearest',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'nearest',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => 'function(context) {
                        let label = context.dataset.label || "";
                        if (label) {
                            label += ": ";
                        }
                        label += "Rp " + context.parsed.y.toLocaleString("id-ID");
                        return label;
                    }',
                    ],
                ],
                'datalabels' => [
                    'anchor' => 'end',
                    'align' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'offset' => true,
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grace' => '15%',
                ],
            ],
        ];
    }
}
