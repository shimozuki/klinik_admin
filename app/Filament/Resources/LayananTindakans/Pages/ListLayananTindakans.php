<?php

namespace App\Filament\Resources\LayananTindakans\Pages;

use App\Filament\Resources\LayananTindakans\LayananTindakanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLayananTindakans extends ListRecords
{
    protected static string $resource = LayananTindakanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Layanan')
                ->icon('heroicon-o-plus'),
        ];
    }
}
