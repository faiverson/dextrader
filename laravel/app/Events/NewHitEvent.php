<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Hit;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewHitEvent extends Event
{
	use SerializesModels;

	public $hit;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Hit $hit)
	{
		$this->hit = $hit;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['hit.new'];
	}
}
