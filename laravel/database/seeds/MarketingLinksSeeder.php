<?php

use Illuminate\Database\Seeder;
use App\Models\MarketingLink;
use App\Models\FunnelCampaign;

class MarketingLinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$fc = FunnelCampaign::create([
			'title' => 'IB'
		]);

		MarketingLink::create([
			'title' => 'IB sales',
			'campaign_id' => $fc->id,
			'description' => 'IB sale page',
			'image' => 'http://dextrader.com/ib',
			'link' => 'http://dextrader.com/ib',
			'products' => '1'
		]);

		MarketingLink::create([
			'title' => 'IB checkout',
			'campaign_id' => $fc->id,
			'description' => 'IB checkout page',
			'image' => 'http://dextrader.com/ib',
			'link' => 'http://secure.dextrader.com/ib',
			'products' => '1,2,3,4',
		]);

		MarketingLink::create([
			'title' => 'IB PRO sales',
			'campaign_id' => $fc->id,
			'description' => 'IB PRO sale page',
			'image' => 'http://dextrader.com/ib-pro',
			'link' => 'http://dextrader.com/ib-pro',
			'products' => '2'
		]);

		MarketingLink::create([
			'title' => 'NA sales',
			'campaign_id' => $fc->id,
			'description' => 'IB PRO sale page',
			'image' => 'http://dextrader.com/na',
			'link' => 'http://dextrader.com/na',
			'products' => '3'
		]);

		MarketingLink::create([
			'title' => 'FX sales',
			'campaign_id' => $fc->id,
			'description' => 'FX sale page',
			'image' => 'http://dextrader.com/fx',
			'link' => 'http://dextrader.com/fx',
			'products' => '4'
		]);

		MarketingLink::create([
				'title' => 'ACADEMY sales',
				'campaign_id' => $fc->id,
				'description' => 'FX sale page',
				'image' => 'http://dextrader.com/academy',
				'link' => 'http://dextrader.com/academy',
				'products' => '5'
		]);

		if (App::Environment() === 'local') {
			$this->fetching();
		}

	}

	public function fetching()
	{
		factory(App\Models\MarketingLink::class, 2)->create();
	}
}
