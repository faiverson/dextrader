<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
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

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'user_id' => 'id',
	];

	protected $getterMutators = [
		'first_name' => 'strtolower|ucwords',
		'last_name' => 'strtolower|ucwords',
		'fullname' => 'strtolower|ucwords'
	];

	protected $setterMutators = [
		'username' => 'strtolower',
		'first_name' => 'strtolower|ucwords',
		'last_name' => 'strtolower|ucwords'
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'first_name', 'last_name', 'username', 'email', 'phone', 'active', 'password'];

	protected $appends = array('full_name', 'user_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'active' , 'deleted_at'];

	public function setPhoneAttribute($value)
	{
		$this->attributes['phone'] = !empty($value) ? preg_replace("/[^0-9]/", "", $value) : '';
	}

	public function getFullnameAttribute()
	{
		return ucfirst($this->attributes['first_name']) . " " . ucfirst($this->attributes['last_name']);
	}

	public function getUserIdAttribute()
	{
		return $this->attributes['id'];
	}

	public function getPhoneAttribute()
	{
		return preg_replace("/(\d{3})(\d{3})(\d{4})/", "$1/$2/$3", $this->attributes['phone']);
	}

//	public function settings()
//	{
//		return $this->hasMany(Settings::class, 'users_settings', 'id', 'user_id');
//	}

}