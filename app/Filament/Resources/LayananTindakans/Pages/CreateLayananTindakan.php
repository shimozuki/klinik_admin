<?php

namespace App\Filament\Resources\LayananTindakans\Pages;

use App\Filament\Resources\LayananTindakans\LayananTindakanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLayananTindakan extends CreateRecord
{
    protected static string $resource = LayananTindakanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Layanan tindakan berhasil ditambahkan';
    }
}
