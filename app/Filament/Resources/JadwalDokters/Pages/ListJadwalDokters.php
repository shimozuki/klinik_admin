<?php
// app/Filament/Resources/JadwalDokters/Pages/ListJadwalDokters.php
namespace App\Filament\Resources\JadwalDokters\Pages;

use App\Filament\Resources\JadwalDokters\JadwalDokterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalDokters extends ListRecords
{
    protected static string $resource = JadwalDokterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
