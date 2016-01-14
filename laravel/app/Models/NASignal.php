<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Eloquence\Eloquence;

class NASignal extends Model
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

	public function getClosePriceAttribute()
	{
		return number_format($this->attributes['close_price'], 5, '.', ',');
	}

	public function setClosePriceAttribute($value)
	{
		$this->attributes['close_price'] = number_format($value, 5, '.', '');
	}

	public function getOpenPriceAttribute()
	{
		return number_format($this->attributes['open_price'], 5, '.', ',');
	}

	public function setOpenPriceAttribute($value)
	{
		$this->attributes['open_price'] = number_format($value, 5, '.', '');
	}

	public function setAssetAttribute($value)
	{
		$this->attributes['asset'] = strlen($value) === 6 ? substr($value, 0, 3) . '/' . substr($value, -3) : $value;
	}
}