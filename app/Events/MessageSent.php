<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $chatType = $this->message->chat_type ?? 'cs';
        $channelPrefix = $chatType === 'admin' ? 'billing-chat.' : 'chat.';

        $channels = [
            new PrivateChannel($channelPrefix . $this->message->receiver_id),
        ];

        $senderId = $this->message->sender_id;

        // For CS chats: Always broadcast to ALL CS/admins so they can see incoming messages
        // The client-side will filter and only display relevant messages
        if ($chatType === 'cs') {
            $csAdminIds = \App\Models\User::whereIn('role', ['administrator', 'admin', 'customer_service'])
                ->pluck('id')
                ->toArray();

            foreach ($csAdminIds as $adminId) {
                // Avoid duplicate channel and don't send to sender
                if ((string)$adminId !== (string)$this->message->receiver_id && 
                    (string)$adminId !== (string)$senderId) {
                    $channels[] = new PrivateChannel($channelPrefix . $adminId);
                }
            }
        }
        // For admin-type (billing) chats: Always broadcast to ALL billing admins
        elseif ($chatType === 'admin') {
            $adminIds = \App\Models\User::whereIn('role', ['administrator', 'admin'])
                ->pluck('id')
                ->toArray();

            foreach ($adminIds as $adminId) {
                // Avoid duplicate channel and don't send to sender
                if ((string)$adminId !== (string)$this->message->receiver_id &&
                    (string)$adminId !== (string)$senderId) {
                    $channels[] = new PrivateChannel($channelPrefix . $adminId);
                }
            }
        }

        return collect($channels)
            ->unique(fn($channel) => $channel->name)
            ->values()
            ->all();
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        // Get sender info with accessor
        $sender = $this->message->sender;

        return [
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
            'sender' => $sender ? [
                'id' => $sender['id'],
                'name' => $sender['name'],
                'email' => $sender['email'] ?? '',
                'role' => $sender['role'] ?? '',
            ] : null,
        ];
    }
}
