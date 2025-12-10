<?php

// app/Filament/Resources/Pasiens/Pages/ViewPasien.php

namespace App\Filament\Resources\Pasiens\Pages;

use App\Filament\Resources\Pasiens\PasienResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewPasien extends ViewRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Hapus Data Pasien')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pasien ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successRedirectUrl(route('filament.admin.resources.pasiens.pasiens.index')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget bisa ditambahkan di sini
        ];
    }
}
