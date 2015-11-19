<?php
namespace App\Models;
use Zizaco\Entrust\EntrustRole;

final class Role extends EntrustRole
{
	protected $table = 'roles';

	protected $hidden = ['id', 'description', 'updated_at', 'created_at', 'pivot'];

//	protected $maps = [
//		'role_id' => 'id',
//	];

	protected $appends = array('role_id');

	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
	}

	public function getRoleIdAttribute()
	{
		return $this->attributes['id'];
	}
}
