<?php

namespace App\Filament\Widgets;

use App\Models\Reservasi;
use App\Models\RekamMedis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBooking = Reservasi::count();
        $bookingSelesai = Reservasi::where('status', 'selesai')->count();
        $bookingPending = Reservasi::where('status', 'pending')->count();
        $bookingBatal = Reservasi::where('status', 'dibatalkan')->count();

        $pendapatan = RekamMedis::sum('biaya');

        $tingkatPenyelesaian = $totalBooking > 0
            ? round(($bookingSelesai / $totalBooking) * 100, 1)
            : 0;

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($pendapatan, 0, ',', '.'))
                ->icon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Booking', $totalBooking)
                ->icon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Tingkat Penyelesaian', $tingkatPenyelesaian . '%')
                ->description("$bookingSelesai dari $totalBooking booking")
                ->icon('heroicon-m-check-circle')
                ->color('warning'),

            Stat::make('Booking Selesai', $bookingSelesai)
                ->icon('heroicon-m-check')
                ->color('success'),
        ];
    }
}
