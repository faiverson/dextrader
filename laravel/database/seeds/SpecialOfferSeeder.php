<?php

use Illuminate\Database\Seeder;
use App\Models\SpecialOffer;
use App\Models\MarketingLink;
use App\Models\Product;

class SpecialOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
			'amount' => 1,
			'countdown' => (60 * 60 * 2) // 2 hs
		]);

		Training::reguard();
		$this->command->info("Specials offers finished");
    }
}
