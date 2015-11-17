<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Role;

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
		$role = Role::where('name', 'owner')->first();
		User::create([
            'first_name' => 'adam',
            'last_name' => 'whiting',
            'username' => 'admin',
            'email' => 'fa.iverson@gmail.com',
            'password' => bcrypt('admin')
        ])->attachRole($role->id);

		$role = Role::where('name', 'admin')->first();
		User::create([
			'first_name' => 'fabian',
			'last_name' => 'torres',
			'username' => 'editor',
			'email' => 'editor@gmail.com',
			'password' => bcrypt('editor')
		])->attachRole($role);

		$role = Role::where('name', 'editor')->first();
		User::create([
			'first_name' => 'luciano',
			'last_name' => 'sixfingers',
			'username' => 'editor',
			'email' => 'luciano.sixfingers@gmail.com',
			'password' => bcrypt('editor')
		])->attachRole($role);

		User::create([
			'first_name' => 'juan',
			'last_name' => 'borda',
			'username' => 'editor',
			'email' => 'juan@borda.com',
			'password' => bcrypt('editor')
		])->attachRole($role);

        // common users
        foreach(range(4, 500) as $index) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'username' => str_replace('.', '_', $faker->unique()->userName),
                'email' => $faker->email,
                'password' => bcrypt('password')
            ]);
        }
    }
}
