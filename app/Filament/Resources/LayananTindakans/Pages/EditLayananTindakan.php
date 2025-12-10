<?php

namespace App\Filament\Resources\LayananTindakans\Pages;

use App\Filament\Resources\LayananTindakans\LayananTindakanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLayananTindakan extends EditRecord
{
    protected static string $resource = LayananTindakanResource::class;

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
        return 'Layanan tindakan berhasil diperbarui';
    }
}
