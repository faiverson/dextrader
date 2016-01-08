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
			'image' => 'dextrader.com/ib',
			'link' => 'dextrader.com/ib',
		]);

		MarketingLink::create([
			'title' => 'IB checkout',
			'campaign_id' => $fc->id,
			'description' => 'IB checkout page',
			'image' => 'dextrader.com/ib',
			'link' => 'secure.dextrader.com/ib',
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
