<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	protected $table = 'payments';

	protected $dates = ['created_at', 'updated_at'];

	protected $fillable = ['id', 'user_id', 'prev_balance', 'balance', 'ledger_type'];
}
