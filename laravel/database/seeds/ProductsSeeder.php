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
		Product::create([
			'name' => 'IB',
			'display_name' => 'IB',
			'amount' => 97,
			'discount' => 0,
		]);

		Product::create([
			'name' => 'PRO',
			'display_name' => 'IB PRO',
			'amount' => 100,
			'discount' => 0,
		]);

		Product::create([
			'name' => 'NA',
			'display_name' => 'NA',
			'amount' => 47,
			'discount' => 0,
		]);

		Product::create([
			'name' => 'FX',
			'display_name' => 'FX',
			'amount' => 85,
			'discount' => 0,
		]);
		Product::reguard();

		$this->command->info("Products finished");

	}
}
