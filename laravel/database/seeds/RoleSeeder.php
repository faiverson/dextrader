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
        $this->command->info("Starting to seed permissions");

		Permission::unguard();
		$uAdd = Permission::create([
			'name' => 'user.add',
			'display_name' => 'Add User',
			'description' => 'Allow to add new users in the system',
		]);
		$uUpdate = Permission::create([
			'name' => 'user.update',
			'display_name' => 'Edit User',
			'description' => 'Allow to update user\'s info in the system',
		]);
		$uDelete = Permission::create([
			'name' => 'user.delete',
			'display_name' => 'Remove User',
			'description' => 'Allow to delete users in the system',
		]);
		$uView = Permission::create([
			'name' => 'user.view',
			'display_name' => 'View User',
			'description' => 'Allow to view users list',
		]);
		$uProfile = Permission::create([
			'name' => 'user.profile',
			'display_name' => 'View profile',
			'description' => 'Allow to view users\' profiles in the system',
		]);
		$uLogin = Permission::create([
			'name' => 'user.login',
			'display_name' => 'Clone User',
			'description' => 'Allow to login as a user in the system',
		]);


		Permission::reguard();
		$this->command->info("Starting to seed roles");
		Role::unguard();

        Role::create([
            'name' => 'owner',
			'display_name' => 'Application God',
			'description' => 'This role can do everything',
        ])->attachPermissions(array($uAdd, $uUpdate, $uDelete, $uView, $uLogin, $uProfile));

        Role::create([
			'name' => 'admin',
			'display_name' => 'Aministrator',
			'description' => 'This role is an admin'
        ])->attachPermissions(array($uAdd, $uUpdate, $uDelete, $uView, $uLogin, $uProfile));

		Role::create([
			'name' => 'editor',
			'display_name' => 'Editor',
			'description' => 'This role is an editor'
		])->attachPermissions(array($uView, $uProfile));

        Role::create([
			'name' => 'user',
			'display_name' => 'User',
			'description' => ''
        ]);

		Role::create([
			'name' => 'coach',
			'display_name' => 'Coach',
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
