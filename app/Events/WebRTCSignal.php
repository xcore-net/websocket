<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignal implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $type;
    public $data;
    public $userId;

    public function __construct($type, $data, $userId)
    {
        $this->type = $type;
        $this->data = $data;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('webrtc.' . $this->userId);
    }
}
