<?php

namespace App\Listeners;

use App\Events\CheckoutFailedEvent;
use App\Libraries\getResponse\GetResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class GetResponsePartialListener implements ShouldQueue
{
	use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
	public function __construct(GetResponse $gr)
	{
		$this->gr = $gr;
		$this->campaign = $this->gr->getCampaignByName('dextrader_partials');
	}

    /**
     * Handle the event.
     *
     * @param  CheckoutFailedEvent  $event
     * @return void
     */
    public function handle(CheckoutFailedEvent $event)
    {
		$contact = $this->addContact($event->data);
	}

	public function addContact($data)
	{
		$custom_fields = ['user_id',
			'first_name',
			'last_name',
			'username',
			'full_name',
			'enroller',
			'enroller_id',
			'billing_address',
			'billing_address2',
			'billing_country',
			'billing_state',
			'billing_city',
			'billing_zip',
			'billing_phone'
		];
		$campaign_id = key($this->campaign);
		$contact = $this->gr->getContactsByEmail($data['email']);
		$name = $data['first_name'] . " " . $data['last_name'];
		if(key($contact) == null) {
			$customs = [];
			foreach ($data as $attribute => $value) {
				if (in_array($attribute, $custom_fields) && !empty($value)) {
					$customs[$attribute] = $value;
				}
			}
			$contact = $this->gr->addContact($campaign_id, $name, $data['email'], 'standard', 0, $customs, $data['ip_address']);
			Log::info('Contact added to dextrader_partials ', (array) $contact);
		}
		else if($contact->{key($contact)}->campaign != $campaign_id) {
			Log::info('Contact moved to dextrader_partials ', (array) $contact);
			$this->gr->moveContactToCampaign(key($contact), $campaign_id);
		}

		return $contact;
	}
}
