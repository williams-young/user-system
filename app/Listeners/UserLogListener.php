<?php

namespace App\Listeners;

use App\Events\UserLogEvent;
use App\Models\UserLog;

class UserLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserLogEvent  $event
     * @return void
     */
    public function handle(UserLogEvent $event)
    {
        UserLog::record($event->action, $event->user_id, $event->model, $event->data);
    }
}
