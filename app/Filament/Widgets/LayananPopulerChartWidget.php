<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LayananPopulerChartWidget extends ChartWidget
{
    protected ?string $heading = 'Layanan Populer';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = DB::table('layanan_tindakan as b')
            ->leftJoin('reservasi as a', 'a.layanan_id', '=', 'b.id')
            ->select('b.nama', DB::raw('COUNT(a.id) as total'))
            ->groupBy('b.nama')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Booking',
                    'data' => $data->pluck('total'),
                    'backgroundColor' => [
                        '#8b5cf6',
                        '#6366f1',
                        '#3b82f6',
                        '#06b6d4',
                        '#10b981',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $data->pluck('nama'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.parsed.x + " booking";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
