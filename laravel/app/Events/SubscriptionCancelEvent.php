<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubscriptionCancelEvent extends Event
{
    use SerializesModels;

	public $subscription;

    public function __construct(Subscription $subscription)
    {
		$this->subscription = $subscription;
    }

    public function broadcastOn()
    {
        return ['subscription.cancel'];
    }
}
