<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;
use Sofa\Eloquence\Mutable;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Eloquence, Mutable, Mappable, Authenticatable, CanResetPassword, EntrustUserTrait, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	protected $maps = [
		'user_id' => 'id',
	];

	protected $getterMutators = [
		'first_name' => 'strtolower|ucwords',
		'last_name' => 'strtolower|ucwords',
		'username' => 'strtolower|ucwords',
		'fullname' => 'strtolower|ucwords',
		'email' => 'strtolower'
	];

	// http://stackoverflow.com/questions/17232714/add-a-custom-attribute-to-a-laravel-eloquent-model-on-load
//	protected $attributes = array(
//		'user_id' => '',
//	);

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'first_name', 'last_name', 'username', 'email', 'phone', 'active', 'password', 'ip_address', 'enroller_id'];

	protected $appends = array('full_name', 'user_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'password', 'remember_token', 'active', 'created_at', 'updated_at', 'deleted_at'];

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

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = bcrypt($value);
	}

	public function setFirstNameAttribute($value)
	{
		$this->attributes['first_name'] = ucwords(strtolower($value));
	}

	public function setLastNameAttribute($value)
	{
		$this->attributes['last_name'] = ucwords(strtolower($value));
	}

	public function setUsernameAttribute($value)
	{
		$this->attributes['username'] = strtolower($value);
	}

	public function setEmailAttribute($value)
	{
		$this->attributes['email'] = strtolower($value);
	}

//	public function settings()
//	{
//		return $this->hasMany(Settings::class, 'users_settings', 'id', 'user_id');
//	}

}