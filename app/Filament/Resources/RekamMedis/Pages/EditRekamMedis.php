<?php

namespace App\Filament\Resources\RekamMedis\Pages;

use App\Filament\Resources\RekamMedis\RekamMedisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRekamMedis extends EditRecord
{
    protected static string $resource = RekamMedisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()->hasRole('dokter') && $this->record->dokter_id === auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rekam medis berhasil diperbarui';
    }

    protected function authorizeAccess(): void
    {
        abort_unless(
            auth()->user()->hasRole('dokter') && $this->record->dokter_id === auth()->id(),
            403,
            'Anda tidak memiliki akses untuk mengedit rekam medis ini.'
        );
    }
}
