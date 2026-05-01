<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $message;
    public $action;

    public function __construct(Message $message, string $action)
    {
        $this->message = $message;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        $chatType = $this->message->chat_type ?? 'cs';
        $channelPrefix = $chatType === 'admin' ? 'billing-chat.' : 'chat.';

        $channels = [
            new PrivateChannel($channelPrefix . $this->message->sender_id),
        ];

        if ($this->message->receiver_id) {
            $channels[] = new PrivateChannel($channelPrefix . $this->message->receiver_id);
        }

        if ($chatType === 'admin') {
            $adminIds = \App\Models\User::whereIn('role', ['administrator', 'admin'])
                ->pluck('id')
                ->toArray();

            foreach ($adminIds as $adminId) {
                $channels[] = new PrivateChannel($channelPrefix . $adminId);
            }
        }

        return collect($channels)
            ->unique(fn($channel) => $channel->name)
            ->values()
            ->all();
    }

    public function broadcastAs(): string
    {
        return 'MessageUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'message' => [
                'id' => $this->message->id,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'message' => $this->message->message,
                'chat_type' => $this->message->chat_type ?? 'cs',
                'is_read' => $this->message->is_read,
                'is_deleted' => (bool) $this->message->is_deleted,
                'edited_at' => optional($this->message->edited_at)->toISOString(),
                'deleted_at' => optional($this->message->deleted_at)->toISOString(),
                'media_url' => $this->message->media_url,
                'media_type' => $this->message->media_type,
                'created_at' => $this->message->created_at->toISOString(),
            ],
        ];
    }
}
