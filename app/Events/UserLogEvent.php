<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserLogEvent
{
    use InteractsWithSockets, SerializesModels;

    public $action;
    public $model;
    public $user_id;
    public $data;

    /**
     * Create a new event instance.
     *
     * @param $action
     * @param object $model
     */
    public function __construct($action, $user_id = 0, $model = null, $data = [])
    {
        $this->action = $action;
        $this->model = $model;
        $this->user_id = $user_id;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
