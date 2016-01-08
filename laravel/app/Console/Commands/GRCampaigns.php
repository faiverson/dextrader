<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\getResponse\GetResponse;
use User;
use Log;

class GRCampaigns extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'getresponse:generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate getresponse list';

	protected $gr;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GetResponse $gr)
    {
		parent::__construct();
        $this->gr = $gr;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
//		$this->addAdminCampaigns();
		$this->addContact();
	}

	public function addAdminCampaign()
	{
		$this->info('Start campaigns');
		$accInfo = $this->gr->getAccountInfo();
		$acs = $this->gr->getAccountFromFields();
		foreach ( $acs as $key => $fields ) {
			// let's find the default account and get FROM_FIELD_ID
			if($accInfo->from_email == $fields->email) {
				$from_field_id = $key;
			}
		}
		$campaign = [
			[
				'name' => 'dextrader_buyers',
				'description' => 'Dextrader Buyers'
			],
			[
				'name' => 'dextrader_partials',
				'description' => 'Dextrader Partials'
			],
			[
				'name' => 'dextrader_leads',
				'description' => 'Dextrader Leads'
			],
			[
				'name' => 'dextrader_inactive',
				'description' => 'Dextrader Inactives'
			]
		];

		$this->info('Check if the campaign are created before');
		$allCampaigns = $this->gr->getCampaigns();
		$existCampaign = [];
		foreach ( $allCampaigns as $showCampaign ) {
			array_push($existCampaign, $showCampaign->name);
			$this->info('Existing campaign: ' . $showCampaign->name);
		}

		foreach ( $campaign as $camp ) {
			if(!in_array($camp['name'], $existCampaign)) {
				$c = $this->gr->createCampaign($camp['name'], $camp['description'], $from_field_id, $from_field_id);
				if (!$c->CAMPAIGN_ID) {
					$this->info('The campaign has not been created. Please try again later or contact with support system.');
				}
				$this->info('Create Campaign: ' . $camp['name']);
			}
		}
	}

	public function addContact()
	{
		$user = User::find(1)->toArray();
		$fields = ['user_id', 'first_name', 'last_name', 'username', 'full_name', 'phone', 'enroller_id'];

		$campaign = $this->gr->getCampaignByName('dextrader_buyers');
//		dd($campaign);
		$contact = $this->gr->getContactsByEmail($user['email']);
//		dd($contact->{key($contact)}->campaign);

		if(key($contact) == null) {
			$this->info('New user: ' . $user['full_name']);
			$customs = [];
			foreach($user as $attribute => $value) {
				if(in_array($attribute, $fields) && !empty($value)) {
					$customs[$attribute] = $value;
				}
			}

			$contact = $this->gr->addContact(key($campaign), $user['full_name'], $user['email'], 'standard', 0, $customs, $user['ip_address']);
		} else {
			$this->info('Move user: ' . $user['full_name']);
			$campaign = $this->gr->getCampaignByName('dextrader_leads');
//			dd($campaign);
			if($contact->{key($contact)}->campaign != key($campaign)) {
				$contact = $this->gr->moveEmailToCampaign($user['email'], 'dextrader_leads');
			}
		}
		Log::info('Contact moved to dextrader_buyers ', (array) $contact);
	}

	public function foo()
	{
		$user = User::find(3)->toArray();
		$fields = ['user_id', 'first_name', 'last_name', 'username', 'full_name', 'phone', 'enroller_id'];
		$campaign = $this->gr->getCampaignByName('dextrader_leads');
		$contact = $this->gr->getContactsByEmail($user['email']);
		if(key($contact) == null) {
			$this->info('New user: ' . $user['full_name']);
			$customs = [];
			foreach($user as $attribute => $value) {
				if(in_array($attribute, $fields) && !empty($value)) {
					$customs[$attribute] = $value;
				}

			}

			$contact = $this->gr->addContact(key($campaign), $user['full_name'], $user['email'], 'standard', 0, $customs, $user['ip_address']);

		} else {
			$this->info('Move user: ' . $user['full_name']);
			$moved = $this->gr->moveEmailToCampaign($user['email'], 'dextrader_leads');
		}
	}
}
