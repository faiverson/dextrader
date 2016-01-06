<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
		$this->call('CitiesSeeder');
		$this->call('CountriesSeeder');
		$this->call('RoleSeeder');
		$this->call('ProductsSeeder');
		$this->call('UserSeeder');
		$this->call('MarketingLinksSeeder');
		$this->call('ProvidersSeeder');
		$this->call('TrainingsSeeder');
		$this->call('PagesSeeder');
		$this->call('TestimonialsSeeder');
		Model::reguard();
    }
}
