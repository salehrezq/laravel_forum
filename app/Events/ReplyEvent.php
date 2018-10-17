<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReplyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reply;
    public $relatedThread;
    public $operation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reply, $relatedThread, $operation)
    {
        $this->reply = $reply;
        $this->relatedThread = $relatedThread;
        $this->operation = $operation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
