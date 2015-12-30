<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Transaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckoutEvent extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
		$this->purchase = $data['transaction'];
		unset($data['transaction']);
		$this->data = $data;
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
