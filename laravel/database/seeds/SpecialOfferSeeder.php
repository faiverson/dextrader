<?php

use Illuminate\Database\Seeder;
use App\Models\SpecialOffer;
use App\Models\MarketingLink;
use App\Models\Product;

class SpecialOfferSeeder extends Seeder
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run()
    {
		$this->command->info("Creating specials offers");
		Training::unguard();

		$mk = MarketingLink::where('link','http://dextrader.com/ib')->first();
		$prod = Product::where('name', 'IB')->first();
		SpecialOffer::create([
			'funnel_id' => $mk->id,
			'product_id' => $prod->id,
			'amount' => 27,
			'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','https://secure.dextrader.com/ib')->first();
		$prod = Product::where('name', 'PRO')->first();
		SpecialOffer::create([
			'funnel_id' => $mk->id,
			'product_id' => $prod->id,
			'amount' => 67,
			'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','https://secure.dextrader.com/ib')->first();
		$prod = Product::where('name', 'NA')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 67,
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','https://secure.dextrader.com/ib')->first();
		$prod = Product::where('name', 'FX')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 97,
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','https://secure.dextrader.com/ib')->first();
		$prod = Product::where('name', 'ACADEMY')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 497,
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','http://dextrader.com/ib')->first();
		$prod = Product::where('name', 'PRO')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 100,
				'type' => 'upsell',
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','http://dextrader.com/ib')->first();
		$prod = Product::where('name', 'PRO')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 67,
				'type' => 'downsell',
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','http://dextrader.com/ib')->first();
		$prod = Product::where('name', 'IB')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 0,
				'type' => 'free-30-days',
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		$mk = MarketingLink::where('link','http://dextrader.com/ib')->first();
		$prod = Product::where('name', 'PRO')->first();
		SpecialOffer::create([
				'funnel_id' => $mk->id,
				'product_id' => $prod->id,
				'amount' => 0,
				'type' => 'free-30-days',
				'countdown' => (60 * 60 * 2) // 2 hs
		]);

		Training::reguard();
		$this->command->info("Specials offers finished");
    }
}
