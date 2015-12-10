<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Purchase extends Model
{
	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchases';

	protected $dates = ['created_at', 'updated_at'];

	protected $maps = [
		'purchase_id' => 'id',
	];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id', 'card_id', 'user_id', 'enroller_id', 'product_id'];

	protected $appends = array('purchase_id');

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