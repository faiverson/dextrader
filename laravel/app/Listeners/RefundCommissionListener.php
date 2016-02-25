<?php

namespace App\Listeners;

use App\Events\RefundEvent;
use App\Gateways\CommissionGateway;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefundCommissionListener implements ShouldQueue
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
	 * @param  RefundEvent  $event
	 * @return void
	 */
	public function handle(RefundEvent $event)
	{
		$this->gateway->refund($event->data);
	}
}