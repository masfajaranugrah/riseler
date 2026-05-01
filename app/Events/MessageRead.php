<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageIds;
    public $userId;
    public $chatType;

    /**
     * Create a new event instance.
     */
    public function __construct($messageIds, $userId, $chatType = 'cs')
    {
        $this->messageIds = $messageIds;
        $this->userId = $userId;
        $this->chatType = $chatType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelPrefix = $this->chatType === 'admin' ? 'billing-chat.' : 'chat.';

        return [
            new PrivateChannel($channelPrefix . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessageRead';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message_ids' => $this->messageIds,
            'user_id' => $this->userId,
            'chat_type' => $this->chatType,
        ];
    }
}
