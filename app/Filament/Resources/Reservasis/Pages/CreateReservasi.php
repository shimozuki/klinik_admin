<?php

namespace App\Filament\Resources\Reservasis\Pages;

use App\Filament\Resources\Reservasis\ReservasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReservasi extends CreateRecord
{
    protected static string $resource = ReservasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Reservasi berhasil dibuat';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['status'] === 'dikonfirmasi' && empty($data['dikonfirmasi_pada'])) {
            $data['dikonfirmasi_pada'] = now();
        }

        if ($data['status'] === 'dibatalkan' && empty($data['dibatalkan_pada'])) {
            $data['dibatalkan_pada'] = now();
        }

        return $data;
    }
}
