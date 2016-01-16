<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	protected $table = 'payments';

	protected $dates = ['created_at', 'updated_at'];

	protected $fillable = ['id', 'user_id', 'prev_balance', 'amount', 'balance', 'ledger_type', 'info', 'paid_dt'];
}
