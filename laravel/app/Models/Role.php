<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

final class Role extends Model
{
    protected $table = 'roles';

    protected $guarded = ['id', 'role'];

    protected $hidden = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->hasMany('App\Models\User', 'role_id', 'id');
    }

}
