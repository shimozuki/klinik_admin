<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;

class ExportLaporanWidget extends Widget
{

    protected function schema(): array
    {
        return [
            Section::make('Export Laporan')
                ->description('Unduh laporan dalam format Excel atau PDF profesional')
                ->icon('heroicon-o-document-arrow-down')
                ->schema([
                    Grid::make(3),
                ])
                ->actions([
                    Action::make('booking')
                        ->label('Laporan Booking')
                        ->icon('heroicon-o-calendar-days'),

                    Action::make('pendapatan')
                        ->label('Laporan Pendapatan')
                        ->icon('heroicon-o-currency-dollar'),

                    Action::make('pelanggan')
                        ->label('Laporan Pelanggan')
                        ->icon('heroicon-o-users'),
                ]),
        ];
    }
}
