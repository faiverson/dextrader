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

		//product permissions
		$ib = Permission::create([
				'name' => 'product.ib',
				'display_name' => 'View IB',
				'description' => 'Allow to view IB section',
		]);
		$training = Permission::create([
				'name' => 'product.ib.training',
				'display_name' => 'Training Certification Viewed',
				'description' => 'Allow to view IB section',
		]);
		$pro = Permission::create([
				'name' => 'product.ib.pro',
				'display_name' => 'View IB PRO',
				'description' => 'Allow to view IB Pro section',
		]);
		$na = Permission::create([
				'name' => 'product.na',
				'display_name' => 'View NA',
				'description' => 'Allow to view NA section',
		]);
		$fx = Permission::create([
				'name' => 'product.fx',
				'display_name' => 'View FX',
				'description' => 'Allow to view FX section',
		]);


		Permission::reguard();
		$this->command->info("Starting to seed roles");
		Role::unguard();

        Role::create([
            'name' => 'owner',
			'display_name' => 'Application God',
			'description' => 'This role can do everything',
        ])->attachPermissions(array($uAdd, $uUpdate, $uDelete, $uView, $uLogin, $uProfile, $ib, $training, $pro, $na, $fx));

        Role::create([
			'name' => 'admin',
			'display_name' => 'Aministrator',
			'description' => 'This role is an admin'
        ])->attachPermissions(array($uAdd, $uUpdate, $uDelete, $uView, $uLogin, $uProfile, $ib, $training, $pro, $na, $fx));

		Role::create([
			'name' => 'editor',
			'display_name' => 'Editor',
			'description' => 'This role is an editor'
		])->attachPermissions(array($uView, $uProfile, $ib, $training, $pro, $na, $fx));

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

		Role::create([
				'name' => 'IB',
				'display_name' => 'IB product',
				'description' => 'This role is related to the IB product',
		])->attachPermission($ib);

		Role::create([
				'name' => 'certification.training',
				'display_name' => 'IB Certification Training',
				'description' => 'When the user has viewed the Certification Training',
		])->attachPermission($training);

		Role::create([
				'name' => 'PRO',
				'display_name' => 'IB PRO product',
				'description' => 'This role is related to the IB PRO product',
		])->attachPermission($pro);

		Role::create([
				'name' => 'NA',
				'display_name' => 'NA product',
				'description' => 'This role is related to the NA product',
		])->attachPermission($na);

		Role::create([
				'name' => 'FX',
				'display_name' => 'FX product',
				'description' => 'This role is related to the FX product',
		])->attachPermission($fx);
		Role::reguard();
    }
}
