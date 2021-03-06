<?php

namespace App\Listeners;

use App\Events\CheckoutEvent;
use App\Gateways\CommissionGateway;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckoutCommissionListener implements ShouldQueue
{
	use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
	public function __construct(CommissionGateway $gateway)
	{
		$this->gateway = $gateway;
	}

    /**
     * Handle the event.
     *
     * @param  CheckoutEvent  $event
     * @return void
     */
	public function handle(CheckoutEvent $event)
	{
		$this->gateway->add($event->data);
	}
}
