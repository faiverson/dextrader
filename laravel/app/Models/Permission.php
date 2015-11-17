<?php
namespace App\Models;
use Zizaco\Entrust\EntrustPermission;

final class Role extends EntrustPermission
{
    protected $table = 'permissions';

    protected $guarded = ['id', 'role'];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->hasMany('App\Models\User', 'role_id', 'id');
    }

}
