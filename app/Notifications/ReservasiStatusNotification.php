<?php

namespace App\Notifications;

use App\Models\Reservasi;
use Illuminate\Notifications\Notification;

class ReservasiStatusNotification extends Notification
{
    public function __construct(
        protected Reservasi $reservasi
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $title = 'Update Status Reservasi';

        $body = match ($this->reservasi->status) {
            'dikonfirmasi' => 'Reservasi kamu telah dikonfirmasi ✅',
            'dibatalkan'   => 'Reservasi kamu dibatalkan ❌',
            'selesai'      => 'Reservasi kamu telah selesai 🦷',
            default        => 'Status reservasi diperbarui',
        };

        return [
            'title' => $title,
            'body'  => $body,
            'type'  => 'reservasi',
            'reservasi_id' => $this->reservasi->id,
            'status' => $this->reservasi->status,
        ];
    }
}
