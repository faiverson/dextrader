<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Hit;
use App\Models\Tag;

class HitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Starting to seed hits");
		$faker = Faker::create();
		$users = \App\Models\User::all();
		$funnels = \App\Models\MarketingLink::all();
		$products = \App\Models\Product::all();

		$total_batch = 10;
		$by_bath = 15;
		foreach(range(1, $total_batch) as $batch) {
			$this->command->info("Starting batch " . $batch . " of " . $total_batch);
			try {
				$user = $users[mt_rand(0, $users->count() - 1)];
//				$user = \App\Models\User::where('id', 63);
				$tags = Tag::where('user_id', $user->id);
				if($tags->count() <= 0) {
					$tag = Tag::create([
						'tag' => $faker->word,
						'user_id' => $user->id
					]);
				} else {
					$tag = $products[mt_rand(0, $tags->count() - 1)];
				}

				foreach (range(1, $by_bath) as $index) {
					$total = $index * $batch - 1;
						$funnel = $funnels[mt_rand(0, $funnels->count() - 1)];
						$product = $products[mt_rand(0, $products->count() - 1)];

						Hit::create([
							'ip_address' => $faker->ipv4,
							'enroller_id' => $user->id,
							'enroller' => $user->username,
							'funnel_id' =>  $funnel->id,
							'product_id' => $product->id,
							'tag' => $tag->display_name,
							'tag_id' => $tag->id,
							'info' => null,
//							'created_at' => $faker->dateTimeThisYear('now'),
//							'updated_at' => $faker->dateTimeThisYear('now')
						]);

				}
			} catch (Exception $e) {
				var_dump($e);
			}
		}
	}
}
