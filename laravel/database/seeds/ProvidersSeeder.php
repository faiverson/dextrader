<?php

use Illuminate\Database\Seeder;

class ProvidersSeeder extends Seeder
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
        factory(App\Models\Provider::class, 20)->create();
    }
}
