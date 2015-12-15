<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Page extends Model implements AuthenticatableContract,
                                    AuthorizableContract
{
    use Eloquence, Mappable, Authenticatable, EntrustUserTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'page_id' => 'id',
	];

	protected $setterMutators = [
		'domain' => 'strtolower'
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'domain', 'site', 'access', 'active', 'password'];

	protected $appends = array('page_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'password', 'access', 'active' , 'deleted_at'];

	public function getPageIdAttribute()
	{
		return $this->attributes['id'];
	}

}