<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hit extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hits';

	protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['ip_address', 'enroller_id', 'funnel_id', 'product_id', 'tag_id', 'info'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'created_at', 'updated_at'];


}