<?php

namespace App\Filament\Resources\Reservasis\Pages;

use App\Filament\Resources\Reservasis\ReservasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservasis extends ListRecords
{
    protected static string $resource = ReservasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Reservasi Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}
