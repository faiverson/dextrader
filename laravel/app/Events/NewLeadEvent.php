<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Lead;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewLeadEvent extends Event
{
	use SerializesModels;

	public $lead;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Lead $lead)
	{
		$this->lead = $lead;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['lead.new'];
	}
}
