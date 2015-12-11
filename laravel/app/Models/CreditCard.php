<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends Model
{
	use SoftDeletes, Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'credit_cards';

	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	protected $maps = [
		'cc_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'name', 'exp_month', 'exp_year', 'number', 'last_four', 'first_six', 'network'];

	protected $appends = array('cc_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'number', 'deleted_at', 'updated_at'];

	public function getCcAttribute()
	{
		return $this->attributes['id'];
	}

	public function getExpMonthAttribute()
	{
		return $this->attributes['exp_month'] < 10 ? '0' . $this->attributes['exp_month'] : (string) $this->attributes['exp_month'];
	}

	public function getExpYearAttribute()
	{
		return $this->attributes['exp_year'] < 10 ? '0' . $this->attributes['exp_year'] : (string) $this->attributes['exp_year'];
	}

}