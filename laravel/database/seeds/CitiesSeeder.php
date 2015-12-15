<?php

use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    public function run()
    {
        DB::table('cities')->delete();
        $cities = Config::get('cities');
        if (!$cities) {
            throw new Exception("Cities config file doesn't exists or empty, did you run: php artisan vendor:publish?");
        }
        DB::table('cities')->insert($cities);
    }
}
