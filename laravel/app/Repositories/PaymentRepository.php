<?php
namespace App\Repositories;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Payment;

class PaymentRepository extends AbstractRepository implements PaymentRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Payment::class;
	}

	public function findLastByUser($user_id)
	{
		return $this->model->where('user_id', $user_id)->orderBy('id', 'desc')->first();
	}

}