<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class BillingAddress extends Model
{
	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'billing_address';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'address_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'user_id', 'name', 'address', 'address2', 'city', 'state', 'country', 'zip', 'phone', 'default_address'];

	protected $appends = array('address_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'number', 'deleted_at', 'updated_at'];

	public function getAddressIdAttribute()
	{
		return $this->attributes['id'];
	}

}