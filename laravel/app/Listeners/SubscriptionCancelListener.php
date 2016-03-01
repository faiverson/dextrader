<?php

namespace App\Listeners;

use App\Events\SubscriptionCancelEvent;
use App\Jobs\SubscriptionCancelJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use DateTime;

class SubscriptionCancelListener //implements ShouldQueue
{
	use DispatchesJobs;

	public function __construct()
	{
	}

    /**
     * Handle the event.
     *
     * @param  SubscriptionCancelEvent $event
     * @return void
     */
    public function handle(SubscriptionCancelEvent $event)
    {
		$now = new DateTime('now');
		$ending = new DateTime($event->subscription->next_billing);
		$time = (int) $ending->format('U') - (int) $now->format('U');
		$job = (new SubscriptionCancelJob($event->subscription))->delay($time);
		$job->onQueue('subscriptions');
		$this->dispatch($job);
    }
}
