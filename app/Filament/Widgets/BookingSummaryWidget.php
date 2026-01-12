<?php

namespace App\Filament\Widgets;

use App\Models\Reservasi;
use App\Models\RekamMedis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingSummaryWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.pages.laporan-analitik');
    }

    protected function getStats(): array
    {
        $totalBooking   = Reservasi::count();
        $bookingSelesai = Reservasi::where('status', 'selesai')->count();
        $bookingPending = Reservasi::where('status', 'pending')->count();
        $bookingBatal   = Reservasi::where('status', 'dibatalkan')->count();

        $totalPendapatan = RekamMedis::sum('biaya');

        $rataRataBooking = $totalBooking > 0
            ? round($totalPendapatan / $totalBooking)
            : 0;

        return [
            Stat::make('Booking Selesai', $bookingSelesai)
                ->description('Booking Selesai')
                ->icon('heroicon-m-check-circle')
                ->chart([
                    5,
                    8,
                    6,
                    10,
                    12,
                    15,
                    $bookingSelesai
                ])
                ->color('success'),

            Stat::make('Booking Pending', $bookingPending)
                ->description('Booking Pending')
                ->icon('heroicon-m-exclamation-circle')
                ->chart([
                    3,
                    4,
                    5,
                    4,
                    6,
                    5,
                    $bookingPending
                ])
                ->color('warning'),

            Stat::make('Booking Batal', $bookingBatal)
                ->description('Booking Batal')
                ->icon('heroicon-m-x-circle')
                ->chart([
                    1,
                    2,
                    3,
                    2,
                    4,
                    3,
                    $bookingBatal
                ])
                ->color('danger'),

            Stat::make(
                'Rata-rata / Booking',
                'Rp ' . number_format($rataRataBooking, 0, ',', '.')
            )
                ->icon('heroicon-m-chart-bar')
                ->chart([
                    150000,
                    175000,
                    200000,
                    180000,
                    210000,
                    225000,
                    $rataRataBooking
                ])
                ->color('info'),
        ];
    }
}
