<?php

namespace App\Listeners;

use App\Events\SubscriptionReactiveEvent;
use App\Gateways\UserGateway;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Libraries\getResponse\GetResponse;
use Log;

class GetResponseReactiveListener implements ShouldQueue
{
	use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
	public function __construct(GetResponse $gr, UserGateway $userGateway)
	{
		$this->gr = $gr;
		$this->campaign = $this->gr->getCampaignByName('dextrader_buyers');
		$this->userGateway = $userGateway;
	}

    /**
     * Handle the event.
     *
     * @param  SubscriptionCancelEvent  $event
     * @return void
     */
    public function handle(SubscriptionReactiveEvent $event)
    {
		$user = $this->userGateway->find($event->subscription->user_id);
		$contact = $this->gr->getContactsByEmail($user->email);
		$campaign_id = key($this->campaign);
		// at this point we consider the contact was created
		// and we need to move the account to the inactive list
		if(key($contact) != null && $contact->{key($contact)}->campaign != $campaign_id) {
			Log::info('Contact moved to dextrader_buyers ', (array) $contact);
			$this->gr->moveContactToCampaign(key($contact), $campaign_id);
		}
    }
}
