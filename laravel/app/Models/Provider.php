<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence; // base trait
use Sofa\Eloquence\Mappable;

class Provider extends Model
{
    use Eloquence, Mappable;

    protected $table = 'providers';

    protected $hidden = ['updated_at', 'created_at'];

    protected $maps = [
        'provider_id' => 'id',
    ];

    protected $fillable = ['id', 'image', 'name', 'us_traders', 'min_deposit', 'review', 'web_site'];

    protected $appends = array('provider_id');

    public function getProviderIdAttribute()
    {
        return $this->attributes['id'];
    }

}