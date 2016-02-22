<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingStat extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'marketing_stats';

	protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'user_id',
		'funnel' ,
		'funnel_id',
		'tag',
		'tag_id',
		'user_id',
		'hits',
		'unique_hits',
		'ib',
		'na',
		'fx',
		'academy',
		'income',
		'created_at'
	];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'updated_at'];
}