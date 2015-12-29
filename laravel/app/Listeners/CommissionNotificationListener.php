<?php

namespace App\Listeners;

use App\Events\CommissionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommissionNotificationListener
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
     * @param  CommissionEvent  $event
     * @return void
     */
    public function handle(CommissionEvent $event)
    {
        //
    }
}
