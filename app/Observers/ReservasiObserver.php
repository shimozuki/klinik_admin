<?php

namespace App\Observers;

use App\Filament\Resources\Reservasis\ReservasiResource;
use App\Models\Reservasi;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

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
}
