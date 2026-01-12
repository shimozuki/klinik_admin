<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\RekamMedis;
use App\Models\Reservasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPasien = Pasien::count();

        $totalBooking = Reservasi::count();

        $pendapatanHariIni = RekamMedis::whereDate('created_at', today())
            ->sum('biaya');

        $pendapatanBulanIni = RekamMedis::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('biaya');

        $pendapatanTahunIni = RekamMedis::whereYear('created_at', now()->year)
            ->sum('biaya');

        $pasienBaruBulanIni = Pasien::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

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

            Stat::make('Total Booking', number_format($totalBooking))
                ->description('Total seluruh booking')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('info'),


            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Pendapatan hari ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('warning'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total bulan ' . now()->format('F'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),
        ];
    }
}
