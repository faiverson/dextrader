<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$this->command->info("Creating Products");

		Product::unguard();
		$ib = Product::create([
			'name' => 'IB',
			'display_name' => 'IB',
			'amount' => 47,
			'discount' => 0,
			'roles' => 'IB'
		]);

		Product::create([
			'name' => 'PRO',
			'display_name' => 'IB PRO',
			'amount' => 100,
			'discount' => 0,
			'parents' => $ib->id,
			'roles' => 'PRO'
		]);

		Product::create([
			'name' => 'NA',
			'display_name' => 'NA',
			'amount' => 67,
			'discount' => 0,
			'roles' => 'NA'
		]);

		Product::create([
			'name' => 'FX',
			'display_name' => 'FX',
			'amount' => 97,
			'discount' => 0,
			'roles' => 'FX'
		]);
		Product::create([
				'name' => 'ACADEMY',
				'display_name' => 'ACADEMY',
				'amount' => 497,
				'discount' => 0,
				'roles' => 'FX'
		]);
		Product::reguard();

		$this->command->info("Products finished");

	}
}
