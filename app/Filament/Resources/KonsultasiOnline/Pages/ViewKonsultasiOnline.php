<?php

namespace App\Filament\Resources\KonsultasiOnline\Pages;

use App\Filament\Resources\KonsultasiOnline\KonsultasiOnlineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewKonsultasiOnline extends ViewRecord
{
    protected static string $resource = KonsultasiOnlineResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Hanya dokter yang bisa melakukan action
        if (auth()->user()->hasRole('dokter')) {

            // Action: Buka Chat (semua status kecuali dibatalkan)
            if ($this->record->status !== 'dibatalkan') {
                $actions[] = Actions\Action::make('buka_chat')
                    ->label('Buka Chat')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn() => route('chatify.chat', ['id' => $this->record->pasien->user_id]))
                    ->openUrlInNewTab();
            }

            // Action: Mulai Konsultasi (hanya status menunggu)
            if ($this->record->status === 'menunggu') {
                $actions[] = Actions\Action::make('mulai_konsultasi')
                    ->label('Mulai Konsultasi')
                    ->icon('heroicon-m-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Konsultasi?')
                    ->modalDescription('Konsultasi akan dimulai dan waktu mulai akan dicatat. Anda akan ditetapkan sebagai dokter yang menangani.')
                    ->modalSubmitActionLabel('Ya, Mulai Konsultasi')
                    ->modalIcon('heroicon-o-play')
                    ->action(function () {
                        $this->record->mulaiKonsultasi();

                        Notification::make()
                            ->success()
                            ->title('Konsultasi Dimulai')
                            ->body('Konsultasi telah dimulai. Silakan lakukan konsultasi dengan pasien.')
                            ->send();
                    })
                    ->after(fn() => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record])));
            }

            // Action: Selesaikan Konsultasi (hanya status berlangsung dan dokter yang handle)
            if ($this->record->status === 'berlangsung' && $this->record->dokter_id === auth()->id()) {
                $actions[] = Actions\Action::make('selesaikan')
                    ->label('Selesaikan Konsultasi')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Selesaikan Konsultasi?')
                    ->modalDescription('Pastikan Anda sudah mengisi catatan dokter dengan lengkap. Konsultasi yang sudah selesai tidak dapat diubah lagi.')
                    ->modalSubmitActionLabel('Ya, Selesaikan')
                    ->modalIcon('heroicon-o-check-circle')
                    ->action(function () {
                        if (empty($this->record->catatan_dokter)) {
                            Notification::make()
                                ->warning()
                                ->title('Catatan Dokter Kosong')
                                ->body('Sebaiknya isi catatan dokter terlebih dahulu sebelum menyelesaikan konsultasi.')
                                ->persistent()
                                ->send();

                            return;
                        }

                        $this->record->selesaikanKonsultasi();

                        Notification::make()
                            ->success()
                            ->title('Konsultasi Selesai')
                            ->body('Konsultasi telah diselesaikan. Pasien dapat melihat catatan dokter.')
                            ->send();
                    })
                    ->after(fn() => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record])));
            }

            // Action: Edit (hanya dokter yang handle dan status belum selesai/dibatalkan)
            if (
                ($this->record->dokter_id === auth()->id() || $this->record->status === 'menunggu') &&
                !in_array($this->record->status, ['selesai', 'dibatalkan'])
            ) {
                $actions[] = Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square');
            }

            // Action: Batalkan (hanya status menunggu/berlangsung)
            if (in_array($this->record->status, ['menunggu', 'berlangsung'])) {
                $actions[] = Actions\Action::make('batalkan')
                    ->label('Batalkan Konsultasi')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Konsultasi?')
                    ->modalDescription('Konsultasi akan dibatalkan dan tidak dapat dilanjutkan. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->modalIcon('heroicon-o-x-circle')
                    ->action(function () {
                        $this->record->batalkanKonsultasi();

                        Notification::make()
                            ->warning()
                            ->title('Konsultasi Dibatalkan')
                            ->body('Konsultasi telah dibatalkan.')
                            ->send();
                    })
                    ->after(fn() => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record])));
            }
        }

        return $actions;
    }
}
