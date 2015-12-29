<?php

namespace App\Listeners;

use App\Events\LiveSignalEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LiveSignalNotificationListener
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
     * @param  LiveSignalEvent  $event
     * @return void
     */
    public function handle(LiveSignalEvent $event)
    {
        //
    }
}
