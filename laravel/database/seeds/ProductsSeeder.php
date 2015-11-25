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

		$this->command->info("Starting to create products");

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

		$this->command->info("Starting to create permission per product");
		Permission::unguard();
		$ib = Permission::create([
			'name' => 'product.ib',
			'display_name' => 'View IB',
			'description' => 'Allow to view IB section',
		]);

		$pro = Permission::create([
			'name' => 'product.pro',
			'display_name' => 'View IB PRO',
			'description' => 'Allow to view IB Pro section',
		]);

		$na = Permission::create([
			'name' => 'product.na',
			'display_name' => 'View NA',
			'description' => 'Allow to view NA section',
		]);

		$fx = Permission::create([
			'name' => 'product.fx',
			'display_name' => 'View FX',
			'description' => 'Allow to view FX section',
		]);
		Permission::unguard();

		$this->command->info("Starting to create roles per product");
		Role::unguard();
		Role::create([
			'name' => 'IB',
			'display_name' => 'IB product',
			'description' => 'This role is related to the IB product',
		])->attachPermission($ib);

		Role::create([
			'name' => 'pro',
			'display_name' => 'IB PRO product',
			'description' => 'This role is related to the IB PRO product',
		])->attachPermission($pro);

		Role::create([
			'name' => 'NA',
			'display_name' => 'NA product',
			'description' => 'This role is related to the NA product',
		])->attachPermission($na);

		Role::create([
			'name' => 'FX',
			'display_name' => 'FX product',
			'description' => 'This role is related to the FX product',
		])->attachPermission($fx);
		Role::reguard();

		$this->command->info("Products finished");

	}
}
