<?php

namespace App\Listeners;

use App\Events\SubscriptionRenewedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Gateways\CommissionGateway;

class SubscriptionCommissionListener implements ShouldQueue
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
     * @param  SubscriptionRenewedEvent  $event
     * @return void
     */
    public function handle(SubscriptionRenewedEvent $event)
    {
		$this->gateway->add($event->data->toArray());
    }
}
