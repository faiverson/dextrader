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
		Page::create([
			'site' => 'dextrader.com',
			'domain' => 'sales',
			'password' => bcrypt('sAles_dexTr4d3r'),
			'access' => 'checkout'
		]);

		Page::create([
			'site' => 'signals.com',
			'domain' => 'signals',
			'password' => bcrypt('siGN4l_dexTr4d3r'),
			'access' => 'signal'
		]);
    }
}
