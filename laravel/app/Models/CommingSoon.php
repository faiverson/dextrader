<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;
use Nicolaslopezj\Searchable\SearchableTrait;

class CommingSoon extends Model
{
//	use Eloquence, Mappable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leads_products_comming_soon';

	protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['user_id', 'product_id', 'email', 'phone'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'created_at', 'updated_at'];

	public function users()
	{
		return $this->hasOne(User::class, 'id', 'user_id')->select(['user_id', 'first_name', 'last_name', 'username', 'email']);
	}

}