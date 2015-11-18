<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
//use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Middleware\EntrustPermission;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Eloquence, Mappable, Authenticatable, CanResetPassword, EntrustUserTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

	protected $dates = ['created_at', 'updated_at', 'disabled_at'];

	protected $maps = [
//		'id' => 'user_id',
		'name' => 'roles.display_name'
	];

	protected $getterMutators = [
		'first_name' => 'strtolower|ucwords',
		'last_name' => 'strtolower|ucwords',
		'fullname' => 'strtolower|ucwords'
	];

	protected $setterMutators = [
		'first_name' => 'strtolower|ucwords',
		'last_name' => 'strtolower|ucwords'
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['first_name', 'last_name', 'username', 'email', 'active', 'password'];

	protected $appends = array('full_name');
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'active' ,'deleted_at', 'updated_at'];

	public function getFullnameAttribute()
	{
		return ucfirst($this->attributes['first_name']) . " " . ucfirst($this->attributes['last_name']);
	}

}