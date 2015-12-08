<?php

namespace App\Listeners;

use App\Events\NotificationEvent;

class NotificationListener
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  PodcastWasPurchased  $event
	 * @return void
	 */
	public function handle(NotificationEvent $event)
	{

	}
}