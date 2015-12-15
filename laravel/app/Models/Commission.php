<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Commission extends Model
{
	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commissions';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'commission_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'amount', 'from_user_id', 'to_user_id', 'purchase_id', 'amount', 'payout_dt', 'refund_dt', 'refund_by', 'created_at', 'updated_at'];

	protected $appends = array('commission_id');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id'];

	public function getPurchaseIdAttribute()
	{
		return $this->attributes['id'];
	}

}