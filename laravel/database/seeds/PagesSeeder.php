<?php

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		if (App::Environment() === 'local') {
			$this->fetching();
		}
		else {

			Page::create([
				'site' => 'secure.dextrader.com',
				'domain' => 'sales',
				'password' => 'sAles_dexTr4d3r',
				'access' => 'checkout,adduser,hits'
			]);

			Page::create([
				'site' => '162.219.24.11',
				'domain' => 'signals',
				'password' => 'siGN4l_dexTr4d3r',
				'access' => 'signal'
			]);
		}

    }

	public function fetching()
	{
		Page::create([
			'site' => 'secure.dx.com',
			'domain' => 'sales',
			'password' => 'sAles_dexTr4d3r',
			'access' => 'checkout,adduser,hits'
		]);

		Page::create([
			'site' => 'local.dx.com',
			'domain' => 'signals',
			'password' => 'siGN4l_dexTr4d3r',
			'access' => 'signal'
		]);
	}
}
