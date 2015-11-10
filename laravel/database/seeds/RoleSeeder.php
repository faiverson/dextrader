<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Starting to seed roles");

        // creates the admin user
        Role::create([
            'id' => 1,
            'role' => 'user',
        ]);

        Role::create([
            'id' => 5,
            'role' => 'editor',
        ]);

		Role::create([
			'id' => 9,
			'role' => 'admin',
		]);

        Role::create([
            'id' => 10,
            'role' => 'superadmin',
        ]);
    }
}
