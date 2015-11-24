<?php

use Illuminate\Database\Seeder;

class MarketingLinksSeeder extends Seeder
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

    }

	public function fetching()
	{
		factory(App\Models\MarketingLink::class, 20)->create();
	}
}
