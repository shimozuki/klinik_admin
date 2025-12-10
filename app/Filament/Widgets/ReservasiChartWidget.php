<?php

namespace App\Filament\Widgets;

use App\Models\Reservasi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReservasiChartWidget extends ChartWidget
{
    protected ?string $heading = 'Reservasi Selesai Per Bulan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getReservasiPerBulan();

        return [
            'datasets' => [
                [
                    'label' => 'Reservasi Selesai',
                    'data' => $data['data'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getReservasiPerBulan(): array
    {
        $bulanIni = now();
        $labels = [];
        $data = [];

        // Ambil data 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $bulan = $bulanIni->copy()->subMonths($i);

            $jumlah = Reservasi::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)
                ->where('status', 'selesai')
                ->count();

            $labels[] = $bulan->format('M Y');
            $data[] = $jumlah;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // Optional: Buat widget bisa di-filter per tahun
    protected function getFilters(): ?array
    {
        return [
            'tahun_ini' => 'Tahun Ini',
            '12_bulan' => '12 Bulan Terakhir',
            'tahun_lalu' => 'Tahun Lalu',
        ];
    }

    protected function applyFilter(array $data, string $filter): array
    {
        switch ($filter) {
            case 'tahun_ini':
                return $this->getReservasiTahunIni();
            case 'tahun_lalu':
                return $this->getReservasiTahunLalu();
            default:
                return $this->getReservasiPerBulan();
        }
    }

    private function getReservasiTahunIni(): array
    {
        $labels = [];
        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $jumlah = Reservasi::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $bulan)
                ->where('status', 'selesai')
                ->count();

            $labels[] = now()->month($bulan)->format('M');
            $data[] = $jumlah;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getReservasiTahunLalu(): array
    {
        $labels = [];
        $data = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $jumlah = Reservasi::whereYear('created_at', now()->subYear()->year)
                ->whereMonth('created_at', $bulan)
                ->where('status', 'selesai')
                ->count();

            $labels[] = now()->month($bulan)->format('M');
            $data[] = $jumlah;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
