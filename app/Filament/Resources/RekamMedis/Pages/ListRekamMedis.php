<?php

namespace App\Filament\Resources\RekamMedis\Pages;

use App\Filament\Resources\RekamMedis\RekamMedisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRekamMedis extends ListRecords
{
    protected static string $resource = RekamMedisResource::class;

    protected function getHeaderActions(): array
    {
        // Hanya dokter yang bisa membuat rekam medis baru
        if (auth()->user()->hasRole('dokter')) {
            return [
                Actions\CreateAction::make()
                    ->label('Buat Rekam Medis')
                    ->icon('heroicon-o-plus'),
            ];
        }

        return [];
    }
}
