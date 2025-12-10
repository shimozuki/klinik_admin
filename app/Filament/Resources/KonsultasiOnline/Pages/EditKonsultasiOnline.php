<?php

namespace App\Filament\Resources\KonsultasiOnline\Pages;

use App\Filament\Resources\KonsultasiOnline\KonsultasiOnlineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKonsultasiOnline extends EditRecord
{
    protected static string $resource = KonsultasiOnlineResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // View action - semua dokter bisa view
        $actions[] = Actions\ViewAction::make()
            ->icon('heroicon-m-eye');

        // Delete action - hanya dokter yang handle dan status menunggu
        if (
            auth()->user()->hasRole('dokter') &&
            ($this->record->dokter_id === auth()->id() || $this->record->status === 'menunggu') &&
            $this->record->status !== 'selesai'
        ) {
            $actions[] = Actions\DeleteAction::make()
                ->icon('heroicon-m-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Konsultasi?')
                ->modalDescription('Konsultasi akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus');
        }

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Konsultasi online berhasil diperbarui';
    }

    protected function authorizeAccess(): void
    {
        // Cek apakah user adalah dokter
        abort_unless(
            auth()->user()->hasRole('dokter'),
            403,
            'Hanya dokter yang dapat mengedit konsultasi online.'
        );

        // Konsultasi yang sudah selesai atau dibatalkan tidak bisa diedit
        abort_if(
            in_array($this->record->status, ['selesai', 'dibatalkan']),
            403,
            'Konsultasi yang sudah ' . $this->record->status . ' tidak dapat diedit.'
        );

        // Hanya dokter yang menangani yang bisa edit (kecuali status masih menunggu)
        if ($this->record->status !== 'menunggu') {
            abort_unless(
                $this->record->dokter_id === auth()->id(),
                403,
                'Anda tidak memiliki akses untuk mengedit konsultasi ini. Hanya dokter yang menangani yang dapat mengedit.'
            );
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jika status diubah ke berlangsung dan belum ada dokter, set dokter yang login
        if ($data['status'] === 'berlangsung' && empty($this->record->dokter_id)) {
            $data['dokter_id'] = auth()->id();

            // Set waktu mulai jika belum ada
            if (empty($data['dimulai_pada'])) {
                $data['dimulai_pada'] = now();
            }
        }

        // Jika status diubah ke selesai, set waktu selesai
        if ($data['status'] === 'selesai' && empty($data['selesai_pada'])) {
            $data['selesai_pada'] = now();
        }

        return $data;
    }
}
