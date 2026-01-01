<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $toUserId;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $toUserId)
    {
        $this->message = $message;
        $this->toUserId = $toUserId;
    }

    /**
     * ✅ PENTING: Broadcast ke channel PENERIMA
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chatify.' . $this->toUserId);
    }

    /**
     * ✅ PENTING: Nama event harus sama dengan Flutter
     */
    public function broadcastAs()
    {
        return 'messaging';
    }

    /**
     * ✅ Data yang dikirim ke client
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }
}
