<?php

namespace App\Filament\Resources\Reservasis\Pages;

use App\Filament\Resources\Reservasis\ReservasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReservasi extends EditRecord
{
    protected static string $resource = ReservasiResource::class;

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
        return 'Reservasi berhasil diperbarui';
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
