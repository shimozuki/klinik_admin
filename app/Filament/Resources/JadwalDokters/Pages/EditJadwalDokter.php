<?php

namespace App\Filament\Resources\JadwalDokters\Pages;

use App\Filament\Resources\JadwalDokters\JadwalDokterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalDokter extends EditRecord
{
    protected static string $resource = JadwalDokterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Jadwal dokter berhasil diperbarui';
    }
}
