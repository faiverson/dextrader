<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Starting to seed Users");
        $faker = Faker::create();

        // creates the admin user
        User::create([
            'first_name' => 'fabian',
            'last_name' => 'torres',
            'username' => 'faiverson',
            'email' => 'fa.iverson@gmail.com',
            'password' => 'admin',
            'role_id' => 10
        ]);

        // common users
        foreach(range(2, 10) as $index) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'username' => str_replace('.', '_', $faker->unique()->userName),
                'email' => $faker->email,
                'password' => $faker->word,
                'role_id' => rand(1,2)
            ]);
        }
    }
}
