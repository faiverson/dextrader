<?php

namespace App\Listeners;

use App\Events\CheckoutFailedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GetResponsePartialListener
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
     * @param  CheckoutFailedEvent  $event
     * @return void
     */
    public function handle(CheckoutFailedEvent $event)
    {
        //
    }
}
