<?php
namespace App\Models;
use Zizaco\Entrust\EntrustPermission;

final class Permission extends EntrustPermission
{
//    protected $table = 'permissions';

	protected $maps = [
		'permission_id' => 'id',
	];
//
	protected $appends = array('permission_id');

	protected $hidden = ['id', 'description', 'updated_at', 'created_at', 'pivot'];

	public function getPermissionIdAttribute()
	{
		return $this->attributes['id'];
	}
}
