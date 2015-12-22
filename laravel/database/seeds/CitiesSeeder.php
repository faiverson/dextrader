<?php

use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run()
    {

        $cities = Config::get('cities');
        if (!$cities) {
            throw new Exception("Cities config file doesn't exists or empty, did you run: php artisan vendor:publish?");
        }
        DB::table('cities')->delete();

        $total_batch = ceil(count($cities) / 1000);
        $this->command->info(count($cities).' '.$total_batch);
        foreach(range(0, $total_batch) as $batch) {
            $this->command->info("Starting batch " . $batch . " of " . $total_batch);
            DB::table('cities')->insert(array_slice($cities, $batch * 1000, 1000));
        }
    }
}
