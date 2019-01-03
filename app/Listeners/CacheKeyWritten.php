<?php

namespace App\Listeners;

use Illuminate\Cache\Events\KeyWritten;

class CacheKeyWritten
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param KeyWritten $event
     * @return void
     */
    public function handle(KeyWritten $event)
    {
    }
}
