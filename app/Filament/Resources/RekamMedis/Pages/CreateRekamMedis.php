<?php

namespace App\Filament\Resources\RekamMedis\Pages;

use App\Filament\Resources\RekamMedis\RekamMedisResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRekamMedis extends CreateRecord
{
    protected static string $resource = RekamMedisResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rekam medis berhasil dibuat';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->hasRole('dokter')) {
            $data['dokter_id'] = auth()->id();
        }

        return $data;
    }

    protected function authorizeAccess(): void
    {
        abort_unless(auth()->user()->hasRole('dokter'), 403, 'Hanya dokter yang dapat membuat rekam medis.');
    }
}
