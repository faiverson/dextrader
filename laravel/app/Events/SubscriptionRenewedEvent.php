<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubscriptionRenewedEvent extends Event
{
	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Invoice $invoice)
	{
		$this->data = $invoice;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['subscription.renewed'];
	}
}
