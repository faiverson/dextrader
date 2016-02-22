<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Purchase;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewPurchaseEvent extends Event
{
	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Purchase $purchase)
	{
		$this->purchase = $purchase;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['purchase.new'];
	}
}
