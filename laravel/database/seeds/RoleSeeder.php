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

		Role::unguard();
        Role::create([
            'name' => 'owner',
			'display_name' => 'Application God',
			'description' => 'This role can do everything',
        ]);

        Role::create([
			'name' => 'admin',
			'display_name' => 'Aministrator',
			'description' => 'This role is an admin'
        ]);

		Role::create([
			'name' => 'editor',
			'display_name' => 'Editor',
			'description' => 'This role is an editor'
		]);

        Role::create([
			'name' => 'user',
			'display_name' => 'User',
			'description' => ''
        ]);

		Role::create([
			'name' => 'lead',
			'display_name' => 'Lead',
			'description' => ''
		]);
		Role::reguard();
    }
}
