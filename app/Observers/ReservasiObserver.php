<?php

namespace App\Observers;

use App\Filament\Resources\Reservasis\ReservasiResource;
use App\Models\Reservasi;
use App\Models\User;
use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use App\Notifications\ReservasiStatusNotification;

class ReservasiObserver
{
    public function created(Reservasi $reservasi): void
    {
        User::role('admin', 'web')->each(function ($admin) use ($reservasi) {
            Notification::make()
                ->title('Reservasi Baru')
                ->body(
                    'Pasien: ' . ($reservasi->pasien->user->name ?? '-') .
                        "\nTanggal: " . $reservasi->tanggal_reservasi
                )
                ->icon('heroicon-o-calendar-days')
                ->success()
                ->actions([
                    Action::make('lihat')
                        ->label('Lihat')
                        ->url(
                            ReservasiResource::getUrl('index')
                        )
                        ->markAsRead(),
                ])
                ->sendToDatabase($admin, isEventDispatched: true);
        });
    }

    public function updated(Reservasi $reservasi): void
    {
        DB::afterCommit(function () use ($reservasi) {

            $pasien = $reservasi->pasien?->user;
            if (!$pasien) return;

            $pasien->notify(
                new ReservasiStatusNotification($reservasi)
            );

            if ($pasien->fcm_token) {
                app(FirebaseService::class)->sendNotification(
                    $pasien->fcm_token,
                    'Update Status Reservasi',
                    'Status reservasi kamu diperbarui',
                    ['type' => 'reservasi']
                );
            }
        });
    }
}
