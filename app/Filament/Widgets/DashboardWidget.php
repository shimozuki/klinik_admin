<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\RekamMedis;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Hitung total pasien
        $totalPasien = Pasien::count();

        // Hitung total dokter
        $totalDokter = User::count();

        // Hitung total pendapatan hari ini
        $pendapatanHariIni = RekamMedis::whereDate('created_at', today())
            ->sum('biaya');

        // Hitung total pendapatan bulan ini
        $pendapatanBulanIni = RekamMedis::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('biaya');

        // Hitung total pendapatan tahun ini
        $pendapatanTahunIni = RekamMedis::whereYear('created_at', now()->year)
            ->sum('biaya');

        // Hitung pasien baru bulan ini
        $pasienBaruBulanIni = Pasien::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Hitung persentase perubahan pasien
        $pasienBulanLalu = Pasien::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $perubahanPasien = $pasienBulanLalu > 0
            ? (($pasienBaruBulanIni - $pasienBulanLalu) / $pasienBulanLalu) * 100
            : 0;

        return [
            Stat::make('Total Pasien', number_format($totalPasien))
                ->description($pasienBaruBulanIni . ' pasien baru bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('Total Dokter', number_format($totalDokter))
                ->description('Dokter aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Pendapatan hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total bulan ' . now()->format('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),
        ];
    }
}
