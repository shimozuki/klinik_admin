<?php

namespace App\Filament\Resources\Pasiens\Pages;

use App\Filament\Resources\Pasiens\PasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPasien extends EditRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->color('info'),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Pasien')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pasien ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pasien berhasil diperbarui';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Anda bisa menambahkan logika tambahan di sini
        return $data;
    }

    protected function afterSave(): void
    {
        // Aksi setelah data diperbarui
        // Contoh: kirim notifikasi, log activity, dll
    }
}
