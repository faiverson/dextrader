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
        $this->command->info("Starting to seed Users");//
        // creates the admin user
		$role = Role::where('name', 'owner')->first();
		User::create([
            'first_name' => 'adam',
            'last_name' => 'whiting',
            'username' => 'admin',
            'email' => 'fa.iverson@gmail.com',
            'password' => bcrypt('admin')
        ])->attachRole($role->id);

		$roleAdmin = Role::where('name', 'admin')->first();
		User::create([
			'first_name' => 'fabian',
			'last_name' => 'torres',
			'username' => 'fabian',
			'email' => 'fabian@gmail.com',
			'password' => bcrypt('admin')
		])->attachRole($roleAdmin);

		$role = Role::where('name', 'editor')->first();
		User::create([
			'first_name' => 'luciano',
			'last_name' => 'sixfingers',
			'username' => 'luciano',
			'email' => 'luciano.sixfingers@gmail.com',
			'password' => bcrypt('editor')
		])->attachRole($role);

		User::create([
			'first_name' => 'juan',
			'last_name' => 'borda',
			'username' => 'juan',
			'email' => 'juan@borda.com',
			'password' => bcrypt('editor')
		])->attachRoles([$role, $roleAdmin]);

		User::create([
			'first_name' => 'jason',
			'last_name' => 'cullins',
			'username' => 'jcullins',
			'email' => 'jason.cullins@innovationstechgroup.com',
			'password' => bcrypt('jcullins')
		])->attachRoles([$roleAdmin]);

		$roleIB = Role::where('name', 'IB')->first();
		User::create([
				'first_name' => 'Dex',
				'last_name' => 'IB',
				'username' => 'dexib',
				'email' => 'dexib@dexib.com',
				'password' => bcrypt('password')
		])->attachRoles([$role, $roleIB]);

		if (App::Environment() === 'local') {
			$this->fakeUsers();
		}
    }

	protected function fakeUsers()
	{
		$faker = Faker::create();
		// common users
		$total_batch = 2;
		foreach(range(1, $total_batch) as $batch) {
			$this->command->info("Starting batch " . $batch . " of " . $total_batch);
			$users = [];
			foreach (range(1, 30) as $index) {
				try {
					$users[] = [
						'first_name' => $faker->firstName,
						'last_name' => $faker->lastName,
						'username' => str_replace('.', '_', $faker->unique()->userName),
						'email' => $faker->unique()->email,
						'password' => bcrypt('password'),
						'created_at' => $faker->dateTimeThisYear('now'),
						'updated_at' => $faker->dateTimeThisYear('now')
					];
				} catch (Exception $e) {
					var_dump($e);
				}
			}
			DB::table('users')->insert($users);
		}
	}
}
