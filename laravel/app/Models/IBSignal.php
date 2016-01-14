<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence;

class IBSignal extends Model
{
	use Eloquence, SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'na_signals';

	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['mt_id', 'signal_time', 'expiry_time', 'direction', 'asset', 'trade_type', 'open_price', 'target_price', 'close_price', 'winloss'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

	public function getTargetPriceAttribute()
	{
		return number_format($this->attributes['target_price'], 2, '.', ',');
	}

	public function setTargetPriceAttribute($value)
	{
		$this->attributes['target_price'] = number_format($value, 2, '.', '');
	}

	public function getEndPriceAttribute()
	{
		return number_format($this->attributes['end_price'], 5, '.', ',');
	}

	public function setEndPriceAttribute($value)
	{
		$this->attributes['end_price'] = number_format($value, 5, '.', '');
	}
}