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

//		$mk = MarketingLink::where('link','http://dextrader.com/ib-pro')->first();
//		$prod = Product::where('name', 'PRO')->first();
//		SpecialOffer::create([
//			'funnel_id' => $mk->id,
//			'product_id' => $prod->id,
//			'amount' => 67,
//			'countdown' => (60 * 60 * 2) // 2 hs
//		]);
//
//		$mk = MarketingLink::where('link','http://dextrader.com/na')->first();
//		$prod = Product::where('name', 'NA')->first();
//		SpecialOffer::create([
//				'funnel_id' => $mk->id,
//				'product_id' => $prod->id,
//				'amount' => 67,
//				'countdown' => (60 * 60 * 2) // 2 hs
//		]);
//
//		$mk = MarketingLink::where('link','http://dextrader.com/fx')->first();
//		$prod = Product::where('name', 'FX')->first();
//		SpecialOffer::create([
//				'funnel_id' => $mk->id,
//				'product_id' => $prod->id,
//				'amount' => 67,
//				'countdown' => (60 * 60 * 2) // 2 hs
//		]);
//
//		$mk = MarketingLink::where('link','http://dextrader.com/academy')->first();
//		$prod = Product::where('name', 'ACADEMY')->first();
//		SpecialOffer::create([
//				'funnel_id' => $mk->id,
//				'product_id' => $prod->id,
//				'amount' => 67,
//				'countdown' => (60 * 60 * 2) // 2 hs
//		]);

		Training::reguard();
		$this->command->info("Specials offers finished");
    }
}
