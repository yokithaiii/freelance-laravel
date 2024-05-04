<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendLikeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $like_str;
    private string $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(string $like_str, int $userId)
    {
        $this->like_str = $like_str;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('send_like_' . $this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'send_like';
    }

    public function broadcastWith(): array
    {
        return [
            'like_str' => $this->like_str,
        ];
    }
}
