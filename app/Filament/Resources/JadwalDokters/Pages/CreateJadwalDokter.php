<?php

namespace App\Filament\Resources\JadwalDokters\Pages;

use App\Filament\Resources\JadwalDokters\JadwalDokterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJadwalDokter extends CreateRecord
{
    protected static string $resource = JadwalDokterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jadwal dokter berhasil ditambahkan';
    }
}
